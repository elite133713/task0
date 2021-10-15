<?php

namespace App\Components\Product\ValueObjects;

use InvalidArgumentException;

/**
 * Class Price
 *
 * @package App\Components\Product\ValueObjects
 */
final class Price
{
    /** @var float */
    private float $price;

    /**
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->setPrice($value);
    }

    /**
     * @return float
     */
    public function price(): float
    {
        return $this->price;
    }

    /**
     * @param float $value
     *
     * @return Price
     */
    private function setPrice(float $value): Price
    {
        if ($value < 0) {
            throw new InvalidArgumentException("The price cannot be less than 0");
        }

        $this->price = $value;

        return $this;
    }
}
