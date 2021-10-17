<?php

namespace App\Components\Product\Entities\Repository;

use App\Components\Product\Entities\ProductContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class ProductRepository
 *
 * @package App\Components\Product\Entities\Repository
 */
class ProductRepository extends AbstractRepository implements ProductRepositoryContract
{
    /**
     * @throws BindingResolutionException
     */
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct(self::CLASS_NAME, self::ALIAS, $manager);
    }

    /**
     * @inheritDoc
     */
    public function byIdentity(Identity $identity): ProductContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof ProductContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?ProductContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function persist(ProductContract $entity): ProductContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function destroy(ProductContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $values, bool $contains = true): ProductRepositoryContract
    {
        $operator = $contains ? 'IN' : 'NOT IN';

        $this->builder()->setParameter('ids', $values);
        $this->builder()->andWhere("{$this->getAlias()}.identity {$operator} (:ids)");

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByCodes(array $values, bool $contains = true): ProductRepositoryContract
    {
        $operator = $contains ? 'IN' : 'NOT IN';

        $this->builder()->setParameter('codes', $values);
        $this->builder()->andWhere("{$this->getAlias()}.code {$operator} (:codes)");

        return $this;
    }
}
