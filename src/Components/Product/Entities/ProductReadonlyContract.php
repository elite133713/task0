<?php

namespace App\Components\Product\Entities;

use App\Components\Product\ValueObjects\Stock;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use DateTimeInterface;

/**
 * Interface ProductReadonlyContract
 *
 * @package App\Components\Product\Entities
 */
interface ProductReadonlyContract extends IdentifiableContract, TimestampableContract
{
    #region COLUMNS
    public const COLUMN_NAME = 'name';

    public const COLUMN_DESCRIPTION = 'description';

    public const COLUMN_CODE = 'code';

    public const COLUMN_PRICE = 'price';

    public const COLUMN_STOCK = 'stock';

    public const COLUMN_DISCONTINUED_AT = 'discontinued_at';

    #endregion

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function description(): string;

    /**
     * @return string
     */
    public function code(): string;

    /**
     * @return float
     */
    public function price(): float;

    /**
     * @return int
     */
    public function stock(): int;

    /**
     * @return DateTimeInterface
     */
    public function discontinuedAt(): DateTimeInterface;
}