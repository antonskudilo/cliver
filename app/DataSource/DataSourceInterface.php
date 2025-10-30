<?php

namespace App\DataSource;

interface DataSourceInterface
{
    /**
     * @param string $table
     * @param mixed $value
     * @param string $field
     * @return array|null
     */
    public function find(string $table, mixed $value, string $field = 'id'): ?array;

    /**
     * @param string $table
     * @param array $conditions
     * @param int|null $limit
     * @param int|null $offset
     * @param array<string, 'ASC'|'DESC'> $orderBy
     * @return iterable<array>
     */
    public function get(string $table, array $conditions = [], ?int $limit = null, ?int $offset = null, array $orderBy = []): iterable;
}
