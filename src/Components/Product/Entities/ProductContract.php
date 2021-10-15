<?php

namespace App\Components\Product\Entities;

use App\Components\Product\ValueObjects\Code;
use App\Components\Product\ValueObjects\Description;
use App\Components\Product\ValueObjects\Name;
use App\Components\Product\ValueObjects\Price;
use App\Components\Product\ValueObjects\Stock;
use DateTime;

/**
 * Interface ProductContract
 *
 * @package App\Components\Product\Entities
 */
interface ProductContract extends ProductReadonlyContract
{
    /**
     * @param Name $value
     *
     * @return ProductEntity
     */
    public function changeName(Name $value): ProductContract;

    /**
     * @param Description $value
     *
     * @return ProductContract
     */
    public function changeDescription(Description $value): ProductContract;

    /**
     * @param Code $value
     *
     * @return ProductContract
     */
    public function changeCode(Code $value): ProductContract;

    /**
     * @param Price $value
     *
     * @return ProductContract
     */
    public function changePrice(Price $value): ProductContract;

    /**
     * @param Stock $value
     *
     * @return ProductContract
     */
    public function changeStock(Stock $value): ProductContract;

    /**
     * @param DateTime|null $discontinued_at
     *
     * @return ProductContract
     */
    public function changeDiscontinuedAt(?DateTime $discontinued_at): ProductContract;
}
