<?php

namespace App\Convention\Repositories\Abstracts;

use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;
use Illuminate\Support\Collection;

/**
 * Class AbstractRepository
 *
 * @package App\Convention\Repositories\Abstracts
 */
abstract class AbstractRepository implements RepositoryContract
{
    /**
     * @var EntityManagerInterface|null
     */
    private ?EntityManagerInterface $manager = null;

    /**
     * @var EntityRepository|null
     */
    private ?EntityRepository $entityRepository = null;

    /**
     * @var QueryBuilder
     */
    private QueryBuilder $builder;

    /**
     * @var string
     */
    private string $className;

    /**
     * @var string
     */
    private string $alias;

    /**
     * @param string                 $className
     * @param string                 $alias
     * @param EntityManagerInterface $manager
     *
     * @throws BindingResolutionException
     */
    public function __construct(string $className, string $alias, EntityManagerInterface $manager)
    {
        $this->className = $className;
        $this->alias = $alias;
        $this->manager = $manager;

        $this->refreshBuilder();
    }

    /**
     * @return EntityManagerInterface
     */
    protected function manager(): EntityManagerInterface
    {
        return $this->manager;
    }

    /**
     * @return EntityRepository
     * @throws UnexpectedValueException
     */
    protected function entityRepository(): EntityRepository
    {
        if (!$this->entityRepository instanceof EntityRepository) {
            /**
             * @var EntityRepository $entityRepository
             */
            $entityRepository = $this->manager->getRepository($this->getClassName());

            $this->entityRepository = $entityRepository;
        }

        return $this->entityRepository;
    }

    /**
     * @return QueryBuilder
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    protected function builder(): QueryBuilder
    {
        if (!$this->builder instanceof QueryBuilder) {
            $this->setBuilder();
        }

        return $this->builder;
    }

    /**
     * @return QueryBuilder
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    private function setBuilder(): QueryBuilder
    {
        $this->builder = $this->entityRepository()->createQueryBuilder($this->getAlias());

        return $this->builder;
    }

    /**
     * @return QueryBuilder
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    protected function refreshBuilder(): QueryBuilder
    {
        return $this->setBuilder();
    }

    /**
     * @return string
     * @throws UnexpectedValueException
     */
    protected function getClassName(): string
    {
        if (strEmpty($this->className)) {
            throw new UnexpectedValueException('Class name cannot be empty');
        }

        return $this->className;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getAlias(): string
    {
        if (strEmpty($this->alias)) {
            throw new InvalidArgumentException('Alias cannot be empty');
        }

        return $this->alias;
    }

    /**
     * @param string $relation
     *
     * @return string
     */
    protected function getRelationAlias(string $relation): string
    {
        $relation = strtolower(Str::snake($relation));

        return "relation_{$relation}_alias";
    }

    /**
     * @param Identity $identity
     *
     * @return object|null
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    protected function direct(Identity $identity): ?object
    {
        if ($this->builder()->getDQLPart('where') !== null) {
            Log::warning((string)json_encode($this->builder()->getDQLPart('where')));

            throw new RuntimeException('DQL cannot be used with query by identity');
        }

        return $this->entityRepository()->find($identity);
    }

    /**
     * @inheritdoc
     */
    public function join(
        string $relation,
        string $alias = null,
        string $type = self::INNER_JOIN_TYPE,
        bool $addSelect = false
    ): string {
        $alias = $alias ?? $this->getAlias();

        $nestedRelationAlias = $alias . '_' . $this->getRelationAlias($relation);

        if (!in_array($nestedRelationAlias, $this->builder()->getAllAliases(), true)) {
            if ($type === self::LEFT_JOIN_TYPE) {
                $this->builder()->leftJoin("{$alias}.{$relation}", $nestedRelationAlias);
            } else {
                $this->builder()->innerJoin("{$alias}.{$relation}", $nestedRelationAlias);
            }
        }

        if ($addSelect) {
            $this->builder()->addSelect($nestedRelationAlias);
        }

        return $nestedRelationAlias;
    }

    /**
     * @inheritdoc
     */
    public function getAll(): Collection
    {
        $results = $this->builder()->getQuery()->getResult();

        $this->refreshBuilder();

        return collect($results);
    }

    /**
     * @inheritDoc
     */
    public function byIds(array $values, bool $contains = true): RepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.identity IN (:ids)")->setParameter('ids', $values);
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.identity NOT IN (:ids)")->setParameter('ids', $values);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMaxResults(int $numberOfResults): RepositoryContract
    {
        $this->builder()->setMaxResults($numberOfResults);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOffset(int $offset): RepositoryContract
    {
        $this->builder()->setFirstResult($offset);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function sortBy(array $data, string $alias = null): RepositoryContract
    {
        $alias = $alias ?? $this->getAlias();

        foreach ($data as $relation => $column) {
            if (is_string($relation)) {
                switch ($relation) {
                    default:
                        $nestedRelationAlias = $this->join(Str::camel($relation), $alias, 'left');
                        $this->sortBy($column, $nestedRelationAlias);
                    break;
                }
            } elseif (is_array($column)) {
                foreach ($column as $subject) {
                    $subject = (string)$subject;

                    $route = strpos($subject, '-') === false ? 'ASC' : 'DESC';

                    $subject = str_replace('-', '', $subject);
                    $subject = Str::camel($subject);

                    $this->builder()->addOrderBy("{$alias}.{$subject}", $route);
                }
            } else {
                $subject = (string)$column;

                $route = strpos($subject, '-') === false ? 'ASC' : 'DESC';

                $subject = str_replace('-', '', $subject);
                $subject = Str::camel($subject);

                $this->builder()->addOrderBy("{$alias}.{$subject}", $route);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $result = $this->builder()->select("count({$this->getAlias()}.identity)")->getQuery()->getSingleScalarResult();

        $this->refreshBuilder();

        return $result;
    }
}
