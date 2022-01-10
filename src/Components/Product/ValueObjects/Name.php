<?php

namespace App\Components\Product\ValueObjects;

use App\Convention\ValueObjects\Contracts\Stringable;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

/**
 * Class Name
 *
 * @package App\Components\Product\ValueObjects
 */
final class Name implements Stringable
{
    /** @var string */
    private string $name;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->setName($value);
    }

    /**
     * @param string $value
     *
     * @return Name
     */
    private function setName(string $value): Name
    {
        $value = trim($value);

        if (strEmpty($value)) {
            throw new InvalidArgumentException("The name cannot be empty");
        }

        if (mb_strlen($value) > 50) {
            throw new InvalidArgumentException('The Name cannot be longer than 50 characters');
        }

        $this->name = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    #[Pure] public function __toString(): string
    {
        return $this->toString();
    }
}
