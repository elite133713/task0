<?php

namespace App\Convention\Entities\Contracts;

use DateTime;

/**
 * Interface TimestampableContract
 *
 * @package App\Convention\Entities\Contracts
 */
interface TimestampableContract extends HasCreatedAt
{
    public const COLUMN_UPDATED_AT = 'updated_at';

    /**
     * @return DateTime
     */
    public function updatedAt(): DateTime;
}