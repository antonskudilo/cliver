<?php

namespace App\DataSource;

final readonly class CompositeDataSource implements DataSourceInterface
{
    /**
     * @param DataSourceInterface[] $sources
     */
    public function __construct(private array $sources) {}

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
        $found = 0;

        foreach ($this->sources as $source) {
            foreach ($source->get($table, $conditions, $limit, $offset, $orderBy) as $row) {
                yield $row;

                $found++;

                if ($limit && $found >= $limit) {
                    return;
                }
            }
        }
    }

    /**
     * @param string $table
     * @param mixed $value
     * @param string $field
     * @return array|null
     */
    public function find(string $table, mixed $value, string $field = 'id'): ?array
    {
        foreach ($this->sources as $source) {
            $item = $source->find($table, $value, $field);

            if (isset($item)) {
                return $item;
            }
        }

        return null;
    }
}
