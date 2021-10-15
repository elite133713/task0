<?php

namespace App\Assistants\Reader;

use Generator;

/**
 * Interface ReaderContract
 *
 * @package App\Assistants\Reader
 */
interface ReaderContract
{
    /**
     * @param string $path
     *
     * @return ReaderContract
     */
    public function read(string $path): ReaderContract;

    /**
     * @return Generator
     */
    public function rows(): Generator;
}
