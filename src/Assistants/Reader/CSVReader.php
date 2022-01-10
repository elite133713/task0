<?php

namespace App\Assistants\Reader;

use Generator;
use InvalidArgumentException;

/**
 * Class CSVReader
 *
 * @package App\Assistants\Reader
 */
class CSVReader implements ReaderContract
{
    /** @var resource */
    protected $file;

    /**
     * @inheritDoc
     */
    public function read(string $path): ReaderContract
    {
        if (!is_readable($path)) {
            throw new InvalidArgumentException("Could not open the file: $path");
        }

        $this->file = fopen($path, 'r');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function rows(): Generator
    {
        while (!feof($this->file)) {
            yield fgetcsv($this->file, 4096);
        }

        return;
    }
}
