<?php

namespace App\Components\Product\ValueObjects;

use InvalidArgumentException;

/**
 * Class Stock
 *
 * @package App\Components\Product\ValueObjects
 */
final class Stock
{
    /** @var int */
    private int $stock;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->setStock($value);
    }

    /**
     * @return int
     */
    public function stock(): int
    {
        return $this->stock;
    }

    /**
     * @param int $value
     *
     * @return Stock
     */
    private function setStock(int $value): Stock
    {
        if ($value < 0) {
            throw new InvalidArgumentException("The stock cannot be less than 0");
        }

        $this->stock = $value;

        return $this;
    }
}
