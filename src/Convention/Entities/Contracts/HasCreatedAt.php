<?php

namespace App\Convention\Entities\Contracts;

use DateTime;

/**
 * Interface HasCreatedAt
 *
 * @package App\Convention\Entities\Contracts
 */
interface HasCreatedAt
{
    public const COLUMN_CREATED_AT = 'created_at';

    /**
     * @return DateTime
     */
    public function createdAt(): DateTime;
}
