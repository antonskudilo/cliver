<?php

namespace App\Utilities;

use IteratorAggregate;
use RuntimeException;
use Traversable;

final readonly class CsvReader implements IteratorAggregate
{
    public function __construct(
        private string $path,
        private string $separator = ',',
        private string $enclosure = '"',
        private string $escape = '\\',
        private bool $hasHeader = true,
    ) {}

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        if (!is_readable($this->path)) {
            throw new RuntimeException("CSV not readable: $this->path");
        }

        $stream = fopen($this->path, 'r');

        if ($stream === false) {
            throw new RuntimeException("Failed to open CSV: $this->path");
        }

        $headers = null;

        if ($this->hasHeader) {
            $headers = $this->fgetcsv($stream);

            if ($headers === false) {
                throw new RuntimeException("Empty CSV: $this->path");
            }

            $headers = array_map('trim', $headers);
        }

        while (($line = $this->fgetcsv($stream)) !== false) {
            if ($headers) {
                $row = array_combine($headers, $line);

                if ($row === false) {
                    throw new RuntimeException("Failed to read CSV: $this->path");
                }
            } else {
                $row = $line;
            }

            yield $row;
        }

        fclose($stream);
    }

    /**
     * @param $stream
     * @return array|false
     */
    private function fgetcsv($stream): array | false
    {
        return fgetcsv($stream, 0, $this->separator, $this->enclosure, $this->escape);
    }
}
