<?php

namespace App\Convention\Services\Contracts;

use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Interface HasList
 *
 * @package App\Convention\Services\Contracts
 */
interface HasList
{
    /**
     * @param array $filters
     */
    public function filterBy(array $filters): void;

    /**PropertyNotInit
     * @return Collection
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     */
    public function listRO(): Collection;

    /**
     * @return int
     * @throws InvalidArgumentException|NoResultException|NonUniqueResultException
     */
    public function count(): int;
}
