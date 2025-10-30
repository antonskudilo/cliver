<?php

namespace App\Collections\Common;

use App\Exceptions\AssertCollectionItemTypeException;
use IteratorAggregate;
use ReflectionProperty;
use RuntimeException;
use Throwable;

/**
 * @template T
 * @implements IteratorAggregate<T>
 */
abstract class BaseCollection implements IteratorAggregate
{
    /**
     * @var T[]
     */
    private array $rows = [];

    /**
     * @return BaseCollectionIterator<T>
     */
    public function getIterator(): BaseCollectionIterator
    {
        return new BaseCollectionIterator($this);
    }

    /**
     * @return class-string<T>
     */
    abstract protected function getItemClass(): string;

    /**
     * @param iterable<T> $rows
     * @return static
     * @throws Throwable
     */
    public static function fromIterable(iterable $rows): static
    {
        $collection = new static();

        foreach ($rows as $row) {
            $collection->addRow($row);
        }

        return $collection;
    }

    /**
     * @param int $position
     * @return T|null
     */
    public function getRow(int $position): mixed
    {
        if (isset($this->rows[$position])) {
            return $this->rows[$position];
        }

        return null;
    }

    /**
     * @return T[]
     */
    public function all(): array
    {
        return $this->rows;
    }

    /**
     * @param T $row
     * @return void
     * @throws Throwable
     */
    public function addRow(mixed $row): void
    {
        $this->assertItemType($row);
        $this->rows[] = $row;
    }

    /**
     * @throws Throwable
     */
    protected function assertItemType(mixed $row): void
    {
        if (
            !is_object($row)
            || !$row instanceof ($this->getItemClass())
        ) {
            AssertCollectionItemTypeException::for(
                collectionClass: static::class,
                expectedClass: static::class,
                entityClass: static::class,
            );
        }
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->rows);
    }

    /**
     * @param string $field
     * @return float|int
     */
    public function sum(string $field): float|int
    {
        $sum = 0;

        foreach ($this->rows as $item) {
            $value = $this->getFieldValue($item, $field);

            if (is_numeric($value)) {
                $sum += $value;
            }
        }

        return $sum;
    }

    /**
     * Calculate the average value of a numeric field across all items.
     *
     * @param string $field The field name to average.
     * @param int $precision Number of decimal places to round to (default: 2).
     * @return float|int
     */
    public function avg(string $field, int $precision = 2): float|int
    {
        $total = 0;
        $count = 0;

        foreach ($this->rows as $item) {
            $value = $this->getFieldValue($item, $field);

            if (is_numeric($value)) {
                $total += $value;
                $count++;
            }
        }

        if ($count === 0) {
            return 0;
        }

        $average = $total / $count;

        if ($precision > 0) {
            return round($average, $precision);
        }

        return $average;
    }

    /**
     * @param object $item
     * @param string $field
     * @return mixed
     */
    private function getFieldValue(object $item, string $field): mixed
    {
        // Try getter method
        $getter = 'get' . ucfirst($field);

        if (method_exists($item, $getter)) {
            return $item->{$getter}();
        }

        // Try isX() for booleans
        $isMethod = 'is' . ucfirst($field);

        if (method_exists($item, $isMethod)) {
            return $item->{$isMethod}();
        }

        // Try property access
        if (property_exists($item, $field)) {
            $reflection = new ReflectionProperty($item, $field);

            if ($reflection->isPublic()) {
                return $item->{$field};
            }
        }

        throw new RuntimeException("Field '{$field}' not found in " . $item::class);
    }
}
