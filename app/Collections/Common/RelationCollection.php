<?php

namespace App\Collections\Common;

use App\Pivots\Common\PivotModel;
use Throwable;

/**
 * @template TEntity
 * @template TPivot of PivotModel
 * @extends BaseCollection<TEntity>
 */
abstract class RelationCollection extends BaseCollection
{
    /**
     * @var array<int, TPivot>
     */
    private array $pivots = [];

    /**
     * @param iterable<TEntity> $rows
     * @param array<int, TPivot> $pivots
     * @return static
     * @throws Throwable
     */
    public static function fromIterable(iterable $rows, array $pivots = []): static
    {
        $collection = new static();

        $index = 0;

        foreach ($rows as $row) {
            $collection->addRow($row);

            if (isset($pivots[$index])) {
                $collection->setPivot($row, $pivots[$index]);
            }

            $index++;
        }

        return $collection;
    }

    /**
     * @param TEntity $entity
     * @param TPivot $pivot
     * @return void
     */
    public function setPivot(object $entity, PivotModel $pivot): void
    {
        $this->pivots[spl_object_id($entity)] = $pivot;
    }

    /**
     * @param TEntity $entity
     * @return TPivot|null
     */
    public function getPivot(object $entity): ?PivotModel
    {
        return $this->pivots[spl_object_id($entity)] ?? null;
    }

    /**
     * Проверяет, есть ли pivot-данные в коллекции.
     *
     * @return bool
     */
    public function hasPivots(): bool
    {
        return !empty($this->pivots);
    }
}