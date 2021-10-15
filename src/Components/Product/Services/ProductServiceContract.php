<?php

namespace App\Components\Product\Services;

use App\Components\Product\Entities\ProductReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\HasList;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Support\Collection;

/**
 * Interface ProductServiceContract
 *
 * @package App\Components\Product\Services
 */
interface ProductServiceContract extends HasList
{
    /**
     * @param string $id
     *
     * @return ProductServiceContract
     * @throws NotFoundException
     */
    public function workWith(string $id): ProductServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return ProductReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): ProductReadonlyContract;

    /**
     * @return Collection
     * @throws NotFoundException
     * @throws PropertyNotInit
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws NotFoundException
     * @throws PropertyNotInit
     */
    public function listRO(): Collection;

    /**
     * @param array $data
     *
     * @return ProductServiceContract
     * @throws Exception
     */
    public function create(array $data): ProductServiceContract;

    /**
     * @param array $data
     *
     * @return ProductServiceContract
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws Exception
     */
    public function change(array $data): ProductServiceContract;

    /**
     * @return ProductServiceContract
     * @throws PropertyNotInit
     */
    public function remove(): ProductServiceContract;
}
