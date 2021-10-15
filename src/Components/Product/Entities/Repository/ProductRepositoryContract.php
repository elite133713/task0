<?php

namespace App\Components\Product\Entities\Repository;

use App\Components\Product\Entities\ProductContract;
use App\Components\Product\Entities\ProductEntity;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Interface ProductRepositoryContract
 *
 * @package App\Components\Product\Entities\Repository
 */
interface ProductRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = ProductEntity::class;

    public const ALIAS = 'product';

    /**
     * @param Identity $identity
     *
     * @return ProductContract
     * @throws NotFoundException
     */
    public function byIdentity(Identity $identity): ProductContract;

    /**
     * @return ProductContract|null
     * @throws NonUniqueResultException
     */
    public function getOne(): ?ProductContract;

    /**
     * @param ProductContract $entity
     *
     * @return ProductContract
     */
    public function persist(ProductContract $entity): ProductContract;

    /**
     * @param ProductContract $entity
     *
     * @return bool
     */
    public function destroy(ProductContract $entity): bool;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return ProductRepositoryContract
     */
    public function filterByIds(array $values, bool $contains = true): ProductRepositoryContract;
}