<?php

namespace App\Components\Product\Services;

use App\Components\Product\Entities\ProductContract;
use App\Components\Product\Entities\ProductEntity;
use App\Components\Product\Entities\ProductReadonlyContract;
use App\Components\Product\Entities\Repository\ProductRepositoryContract;
use App\Components\Product\ValueObjects\Code;
use App\Components\Product\ValueObjects\Description;
use App\Components\Product\ValueObjects\Name;
use App\Components\Product\ValueObjects\Price;
use App\Components\Product\ValueObjects\Stock;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\HasListTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class ProductService
 *
 * @package App\Components\Product\Services
 */
class ProductService implements ProductServiceContract
{
    use HasListTrait;

    /**
     * @var ProductContract|null
     */
    private ?ProductContract $entity = null;

    /**
     * @var ProductRepositoryContract
     */
    private ProductRepositoryContract $repository;

    /**
     * @param ProductRepositoryContract $repository
     */
    public function __construct(ProductRepositoryContract $repository) {
        $this->repository = $repository;
    }

    /**
     * @return ProductContract
     * @throws PropertyNotInit
     */
    private function _entity(): ProductContract
    {
        if (!$this->entity instanceof ProductContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param ProductContract $entity
     *
     * @return ProductServiceContract
     */
    private function setEntity(ProductContract $entity): ProductServiceContract
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): ProductServiceContract
    {
        return $this->setEntity($this->repository->byIdentity(new Identity($id)));
    }

    /**
     * @inheritDoc
     */
    public function identity(): Identity
    {
        return $this->_entity()->identity();
    }

    /**
     * @inheritDoc
     */
    public function readonly(): ProductReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(fn(ProductReadonlyContract $entity) => $entity);
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->handleFilters($this->filters());

        $results = $this->repository->getAll();

        $this->filterBy([]);

        return $results;
    }

    /**
     * @param array $filter
     */
    private function handleFilters(array $filter): void
    {
        if (Arr::has($filter, 'ids')) {
            $needleScopes = Arr::get($filter, 'ids.scopes.collection', []);
            $isContains = filter_var(Arr::get($filter, 'ids.scopes.has', true), FILTER_VALIDATE_BOOLEAN);
            $this->repository->filterByIds($needleScopes, $isContains);
        }
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): ProductServiceContract
    {
        $this->filterBy(
            [
                'codes' => [
                    'scopes' => [
                        'collection' => [Arr::get($data, ProductReadonlyContract::COLUMN_CODE, '')],
                        'has'        => true,
                    ],
                ],
            ]
        );

        if ($this->count()) {
            throw new InvalidArgumentException('The code is already exists');
        }

        $this->setEntity($this->make($data));
        $this->repository->persist($this->_entity());

        return $this;
    }

    /**
     * @param array $data
     *
     * @return ProductContract
     * @throws Exception
     */
    private function make(array $data): ProductContract
    {
        return new ProductEntity(
            IdentityGenerator::next(),
            new Name(Arr::get($data, ProductReadonlyContract::COLUMN_NAME, '')),
            new Description(Arr::get($data, ProductReadonlyContract::COLUMN_DESCRIPTION, '')),
            new Code(Arr::get($data, ProductReadonlyContract::COLUMN_CODE, '')),
            new Price(Arr::get($data, ProductReadonlyContract::COLUMN_PRICE, 0)),
            new Stock(Arr::get($data, ProductReadonlyContract::COLUMN_STOCK, 0)),
            Arr::get($data, ProductReadonlyContract::COLUMN_DISCONTINUED_AT)
        );
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): ProductServiceContract
    {
        if (Arr::has($data, 'name')) {
            $this->_entity()->changeName(Arr::get($data, 'name'));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): ProductServiceContract
    {
        $this->repository->destroy($this->_entity());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $this->handleFilters($this->filters());

        $result = $this->repository->count();

        $this->filterBy([]);

        return $result;
    }
}
