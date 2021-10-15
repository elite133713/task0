<?php

namespace App\Components\Product\Entities;

use App\Components\Product\ValueObjects\Code;
use App\Components\Product\ValueObjects\Description;
use App\Components\Product\ValueObjects\Name;
use App\Components\Product\ValueObjects\Price;
use App\Components\Product\ValueObjects\Stock;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use InvalidArgumentException;

/**
 * Class ProductEntity
 *
 * @package App\Components\Product\Entities
 */
class ProductEntity implements ProductContract
{
    use IdentifiableTrait;
    use TimestampableTrait;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var string
     */
    private string $code;

    /**
     * @var float
     */
    private float $price;

    /**
     * @var int
     */
    private int $stock;

    /**
     * @var DateTime|null
     */
    private ?DateTime $discontinued_at;

    /**
     * @param Identity      $identity
     * @param Name          $name
     * @param Description   $description
     * @param Code          $code
     * @param Price         $price
     * @param Stock         $stock
     * @param DateTime|null $discontinued_at
     */
    public function __construct(
        Identity $identity,
        Name $name,
        Description $description,
        Code $code,
        Price $price,
        Stock $stock,
        ?DateTime $discontinued_at
    ) {
        $this->setIdentity($identity);
        $this->changeName($name);
        $this->changeDescription($description);
        $this->changeCode($code);
        $this->changePrice($price);
        $this->changeStock($stock);
        $this->changeDiscontinuedAt($discontinued_at);
        $this->created_at = new DateTime();
        $this->updated_at = new DateTime();
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function changeName(Name $value): ProductContract
    {
        $this->name = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function changeDescription(Description $value): ProductContract
    {
        $this->description = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function changeCode(Code $value): ProductContract
    {
        $this->code = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function price(): float
    {
        return $this->price;
    }

    /**
     * @param Price $value
     *
     * @return ProductContract
     */
    public function changePrice(Price $value): ProductContract
    {
        $this->price = $value->price();

        return $this;
    }

    /**
     * @return int
     */
    public function stock(): int
    {
        return $this->stock;
    }

    /**
     * @inheritDoc
     */
    public function changeStock(Stock $value): ProductContract
    {
        $this->stock = $value->stock();

        return $this;
    }

    /**
     * @return DateTime
     */
    public function discontinuedAt(): DateTime
    {
        return $this->discontinued_at;
    }

    /**
     * @inheritDoc
     */
    public function changeDiscontinuedAt(?DateTime $discontinued_at): ProductContract
    {
        $this->discontinued_at = $discontinued_at;

        return $this;
    }
}
