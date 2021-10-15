<?php

namespace App\Components\Product\ValueObjects;

use App\Convention\ValueObjects\Contracts\Stringable;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

/**
 * Class Code
 *
 * @package App\Components\Product\ValueObjects
 */
final class Code implements Stringable
{
    /** @var string */
    private string $code;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->setCode($value);
    }

    /**
     * @param string $value
     *
     * @return Code
     */
    private function setCode(string $value): Code
    {
        $value = trim($value);

        if (strEmpty($value)) {
            throw new InvalidArgumentException("The code cannot be empty");
        }

        if (mb_strlen($value) > 10) {
            throw new InvalidArgumentException('The code cannot be longer than 10 characters');
        }

        $this->code = $value;

        return $this;
    }

    /**
     * @param Code $code
     *
     * @return bool
     */
    public function equals(Code $code): bool
    {
        return $code->toString() === $this->toString();
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    #[Pure] public function __toString(): string
    {
        return $this->toString();
    }
}
