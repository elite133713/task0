<?php

namespace App\Convention\Services\Traits;

/**
 * Trait HasListTrait
 *
 * @package App\Convention\Services\Traits
 */
trait HasListTrait
{
    /**
     * @var array
     */
    private array $filters = [];

    /**
     * @inheritDoc
     */
    public function filterBy(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    private function filters(): array
    {
        return $this->filters;
    }
}
