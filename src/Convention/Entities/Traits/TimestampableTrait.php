<?php

namespace App\Convention\Entities\Traits;

use DateTime;
use Exception;

/**
 * Trait TimestampableTrait
 *
 * @package App\Convention\Entities\Traits
 */
trait TimestampableTrait
{
    use HasCreatedAtTrait;

    /**
     * @var DateTime
     */
    private DateTime $updated_at;

    /**
     * @inheritDoc
     */
    public function updatedAt(): DateTime
    {
        return $this->updated_at;
    }

    /**
     * @throws Exception
     */
    protected function setUpdatedAt(): void
    {
        $this->updated_at = new DateTime();
    }
}
