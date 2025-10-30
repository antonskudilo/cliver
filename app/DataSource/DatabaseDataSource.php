<?php

namespace App\DataSource;

use App\Enums\SortDirectionEnum;
use InvalidArgumentException;
use PDO;
use PDOStatement;

readonly class DatabaseDataSource implements DataSourceInterface
{
    public function __construct(private PDO $pdo) {}

    /**
     * @param string $table
     * @param mixed $value
     * @param string $field
     * @return array|null
     */
    public function find(string $table, mixed $value, string $field = 'id'): ?array
    {
        [$sql, $params] = $this->buildSql($table, [$field => $value], 1);
        $stmt = $this->prepareStatement($sql, $params);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
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
        [$sql, $params] = $this->buildSql($table, $conditions, $limit, $offset, $orderBy);
        $stmt = $this->prepareStatement($sql, $params);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param int|null $limit
     * @param int|null $offset
     * @param array<string, 'ASC'|'DESC'> $orderBy
     * @return array
     */
    private function buildSql(string $table, array $conditions, ?int $limit, ?int $offset = null, array $orderBy = []): array
    {
        $sql = "SELECT * FROM {$table}";
        [$where, $params] = $this->buildConditions($conditions);

        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $params['limit'] = $limit;
        }

        if (!empty($offset)) {
            $sql .= " OFFSET :offset";
            $params['offset'] = $offset;
        }

        if (!empty($orderBy)) {
            $parts = [];

            foreach ($orderBy as $column => $direction) {
                $dir = strtoupper($direction) === SortDirectionEnum::DESC->value
                    ? SortDirectionEnum::DESC->value
                    : SortDirectionEnum::ASC->value;

                $parts[] = "{$column} {$dir}";
            }

            $sql .= " ORDER BY " . implode(', ', $parts);
        }

        $sql .= ';';

        return [$sql, $params];
    }

    /**
     * Преобразует массив условий в SQL выражения и параметры.
     *
     * @param array<string, array<int, array{operator: string, value: mixed}>> $conditions
     * @return array{0: string[], 1: array<string, mixed>}
     */
    private function buildConditions(array $conditions): array
    {
        $where = [];
        $params = [];

        foreach ($conditions as $field => $clauses) {
            foreach ($clauses as $i => $clause) {
                $operator = strtolower(trim($clause['operator']));
                $value = $clause['value'];
                $paramName = "{$field}_{$i}";

                switch ($operator) {
                    case '=':
                    case '==':
                        $where[] = "$field = :$paramName";
                        $params[$paramName] = $value;

                        break;
                    case '!=':
                    case '<>':
                        $where[] = "$field != :$paramName";
                        $params[$paramName] = $value;

                        break;
                    case '>':
                    case '<':
                    case '>=':
                    case '<=':
                        $where[] = "$field $operator :$paramName";
                        $params[$paramName] = $value;

                        break;
                    case 'like':
                        $where[] = "$field LIKE :$paramName";
                        $params[$paramName] = $value;

                        break;
                    case 'not like':
                        $where[] = "$field NOT LIKE :$paramName";
                        $params[$paramName] = $value;

                        break;
                    case 'in':
                    case 'not in':
                        $valueArray = (array)$value;

                        if (empty($valueArray)) {
                            // Если массив пуст — условие всегда ложно (для IN) или всегда истинно (для NOT IN)
                            $where[] = $operator === 'in'
                                ? '0=1'
                                : '1=1';

                            break;
                        }

                        $inPlaceholders = [];

                        foreach ($valueArray as $j => $val) {
                            $paramKey = "{$paramName}_$j";
                            $inPlaceholders[] = ":$paramKey";
                            $params[$paramKey] = $val;
                        }

                        $where[] = sprintf(
                            "%s %s (%s)",
                            $field,
                            strtoupper($operator),
                            implode(', ', $inPlaceholders)
                        );

                        break;
                    case 'is null':
                        $where[] = "$field IS NULL";

                        break;
                    case 'is not null':
                        $where[] = "$field IS NOT NULL";

                        break;
                    default:
                        throw new InvalidArgumentException("Unsupported operator '$operator' for field '$field'");
                }
            }
        }

        return [$where, $params];
    }

    /**
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    private function prepareStatement(string $sql, array $params): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $param = str_starts_with($key, ':') ? $key : ":$key";
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($param, $value, $type);
        }

        return $stmt;
    }
}
