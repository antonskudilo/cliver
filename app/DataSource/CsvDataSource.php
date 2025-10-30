<?php

namespace App\DataSource;

use App\Enums\SortDirectionEnum;
use App\Utilities\CsvReader;
use InvalidArgumentException;
use RuntimeException;

final readonly class CsvDataSource implements DataSourceInterface
{
    public function __construct(private string $dir) {}

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

    /**
     * @param string $table
     * @param mixed $value
     * @param string $field
     * @return array|null
     */
    public function find(string $table, mixed $value, string $field = 'id'): ?array
    {
        foreach ($this->readFile($table) as $row) {
            if (isset($row[$field]) && $row[$field] == $value) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @return iterable<array>
     */
    public function readFile(string $table): iterable
    {
        $file = $this->getFile($table);

        yield from new CsvReader($file);
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param int|null $limit
     * @param int|null $offset
     * @param array<string, 'ASC'|'DESC'> $orderBy
     * @return iterable
     */
    public function get(string $table, array $conditions = [], ?int $limit = null, ?int $offset = null, array $orderBy = []): iterable
    {
        if (empty($orderBy)) {
            return $this->getStreamed($table, $conditions, $limit, $offset);
        }

        return $this->getSorted($table, $conditions, $limit, $offset, $orderBy);
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param int|null $limit
     * @param int|null $offset
     * @return iterable
     */
    private function getStreamed(string $table, array $conditions, ?int $limit, ?int $offset): iterable
    {
        $count = 0;
        $skipped = 0;

        foreach ($this->readFile($table) as $row) {
            if (!empty($conditions) && !$this->matchConditions($row, $conditions)) {
                continue;
            }

            if ($offset !== null && $skipped < $offset) {
                $skipped++;
                continue;
            }

            yield $row;

            $count++;

            if ($limit !== null && $count >= $limit) {
                break;
            }
        }
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param int|null $limit
     * @param int|null $offset
     * @param array $orderBy
     * @return iterable
     */
    private function getSorted(string $table, array $conditions, ?int $limit, ?int $offset, array $orderBy): iterable
    {
        $rows = [];

        foreach ($this->readFile($table) as $row) {
            if (empty($conditions) || $this->matchConditions($row, $conditions)) {
                $rows[] = $row;
            }
        }

        if (!empty($orderBy)) {
            usort($rows, function ($a, $b) use ($orderBy) {
                foreach ($orderBy as $column => $direction) {
                    $direction = strtoupper($direction);
                    $av = $a[$column] ?? null;
                    $bv = $b[$column] ?? null;

                    if ($av == $bv) {
                        continue;
                    }

                    $cmp = $av <=> $bv;

                    return $direction === SortDirectionEnum::DESC->value
                        ? -$cmp
                        : $cmp;
                }

                return 0;
            });
        }

        if ($offset !== null) {
            $rows = array_slice($rows, $offset);
        }

        if ($limit !== null) {
            $rows = array_slice($rows, 0, $limit);
        }

        yield from $rows;
    }

    /**
     * @param array $row
     * @param array $conditions
     * @return bool
     */
    private function matchConditions(array $row, array $conditions): bool
    {
        foreach ($conditions as $field => $clauses) {
            if (!isset($row[$field])) {
                return false;
            }

            $value = $row[$field];

            foreach ($clauses as $clause) {
                $operator = $clause['operator'];
                $target = $clause['value'];

                $match = match ($operator) {
                    '=', '==' => $value == $target,
                    '!=' => $value != $target,
                    '>' => $value > $target,
                    '>=' => $value >= $target,
                    '<' => $value < $target,
                    '<=' => $value <= $target,
                    'in' => in_array($value, (array) $target),
                    'not in' => !in_array($value, (array) $target),
                    'like' => fnmatch(str_replace('%', '*', $target), $value),
                    default => throw new InvalidArgumentException("Unknown operator: $operator"),
                };

                if (!$match) {
                    return false;
                }
            }
        }

        return true;
    }
}