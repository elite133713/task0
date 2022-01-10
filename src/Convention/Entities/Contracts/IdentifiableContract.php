<?php

namespace App\Convention\Entities\Contracts;

use App\Convention\ValueObjects\Identity\Identity;

/**
 * Interface IdentifiableContract
 *
 * @package App\Convention\Entities\Contracts
 */
interface IdentifiableContract
{
    public const COLUMN_ID = 'id';

    /**
     * @return Identity
     */
    public function identity(): Identity;
}
