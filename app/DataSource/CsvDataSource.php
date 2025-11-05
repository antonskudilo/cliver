<?php

namespace App\DataSource;

use RuntimeException;

final readonly class CsvDataSource implements DataSourceInterface
{
    use CsvQueryTrait;

    public function __construct(private string $dir) {}

    /**
     * @param string $table
     * @param mixed $value
     * @param string $field
     * @return array|null
     */
    public function find(string $table, mixed $value, string $field = 'id'): ?array
    {
        $file = $this->getFile($table);

        return $this->findInCsv($file, $value, $field);
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param int|null $limit
     * @param int|null $offset
     * @param array $orderBy
     * @return iterable
     */
    public function get(string $table, array $conditions = [], ?int $limit = null, ?int $offset = null, array $orderBy = []): iterable
    {
        $file = $this->getFile($table);

        return $this->readCsv($file, $conditions, $limit, $offset, $orderBy);
    }

    /**
     * @param string $table
     * @return string
     */
    private function getFile(string $table): string
    {
        $file = "$this->dir/$table.csv";

        if (!file_exists($file)) {
            throw new RuntimeException("CSV file not found: $file");
        }

        return $file;
    }
}
