<?php

namespace App\Components\Product\ValueObjects;

use App\Convention\ValueObjects\Contracts\Stringable;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

/**
 * Class Description
 *
 * @package App\Components\Product\ValueObjects
 */
final class Description implements Stringable
{
    /** @var string */
    private string $description;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->setDescription($value);
    }

    /**
     * @param string $value
     *
     * @return Description
     */
    private function setDescription(string $value): Description
    {
        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('The description cannot be longer than 50 characters');
        }

        $this->description = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    #[Pure] public function __toString(): string
    {
        return $this->toString();
    }
}
