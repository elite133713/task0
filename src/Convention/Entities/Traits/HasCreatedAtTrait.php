<?php

namespace App\Convention\Entities\Traits;

use DateTime;
use Exception;

/**
 * Trait HasCreatedAtTrait
 *
 * @package App\Convention\Entities\Traits
 */
trait HasCreatedAtTrait
{
    /**
     * @var DateTime
     */
    private DateTime $created_at;

    /**
     * @inheritDoc
     */
    public function createdAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * @throws Exception
     */
    protected function setCreatedAt(): void
    {
        $this->created_at = new DateTime();
    }
}
