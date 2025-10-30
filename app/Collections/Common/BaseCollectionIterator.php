<?php

namespace App\Collections\Common;

use Iterator;

/**
 * @template T
 * @implements Iterator<int, T>
 */
class BaseCollectionIterator implements Iterator
{
    protected int $position = 0;

    /**
     * @var BaseCollection<T>
     */
    protected BaseCollection $collection;

    /**
     * @param BaseCollection<T> $collection
     */
    public function __construct(BaseCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return T|null
     */
    public function current(): mixed
    {
        return $this->collection->getRow($this->position);
    }

    /**
     * @return void
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return !is_null($this->collection->getRow($this->position));
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        $this->position = 0;
    }
}
