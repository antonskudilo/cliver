<?php

namespace App\DataSource;

use App\Utilities\CsvReader;
use InvalidArgumentException;

trait CsvQueryTrait
{
    /**
     * @param string $file
     * @param array $conditions
     * @param int|null $limit
     * @param int|null $offset
     * @param array<string, 'ASC'|'DESC'> $orderBy
     * @return iterable<array>
     */
    private function readCsv(string $file, array $conditions = [], ?int $limit = null, ?int $offset = null, array $orderBy = []): iterable
    {
        $rows = iterator_to_array(new CsvReader($file));

        if (!empty($conditions)) {
            $rows = array_filter($rows, fn($row) => $this->matchConditions($row, $conditions));
        }

        if (!empty($orderBy)) {
            usort($rows, function ($a, $b) use ($orderBy) {
                foreach ($orderBy as $column => $direction) {
                    $av = $a[$column] ?? null;
                    $bv = $b[$column] ?? null;

                    if ($av === $bv) {
                        continue;
                    }

                    $cmp = $av <=> $bv;

                    return strtoupper($direction) === 'DESC' ? -$cmp : $cmp;
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
     * @param string $file
     * @param mixed $value
     * @param string $field
     * @return array|null
     */
    private function findInCsv(string $file, mixed $value, string $field = 'id'): ?array
    {
        foreach (new CsvReader($file) as $row) {
            if (($row[$field] ?? null) == $value) {
                return $row;
            }
        }

        return null;
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
                    'in' => in_array($value, (array)$target),
                    'not in' => !in_array($value, (array)$target),
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
