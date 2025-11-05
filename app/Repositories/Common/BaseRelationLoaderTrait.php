<?php

namespace App\Repositories\Common;

use Throwable;

/**
 * @template TModel
 * @template TRepo of BaseRepository<TModel>
 */
trait BaseRelationLoaderTrait
{
    /**
     * @var array<string>
     */
    private array $with = [];

    /**
     * @return void
     */
    private function unsetRelations(): void
    {
        $this->with = [];
    }

    /**
     * @param array<string>|string $relations
     * @return TRepo
     */
    public function withRelation(array|string $relations)
    {
        if (is_string($relations)) {
            $relations = [$relations];
        }

        $this->with = array_unique([...$this->with, ...$relations]);

        return $this;
    }

    /**
     * @template TEntity of object
     * @param iterable<object> $entities
     * @param callable(object): int $idSelector Function to extract the ID from an entity
     * @param callable(int[]): array<int, TEntity> $loader Function that returns an array of related entities [id => entity]
     * @param callable(object, ?TEntity): void $setter Function to assign the related entity to the entity
     * @param bool $isCollection
     */
    public function eagerLoad(
        iterable $entities,
        callable $idSelector,
        callable $loader,
        callable $setter,
        bool $isCollection
    ): void {
        $ids = [];

        foreach ($entities as $entity) {
            $ids[] = $idSelector($entity);
        }

        if (!$ids) {
            return;
        }

        $related = $loader(array_unique($ids));

        foreach ($entities as $entity) {
            $id = $idSelector($entity);
            $value = $related[$id] ?? ($isCollection ? [] : null);

            if ($isCollection) {
                $setter($entity, $value);
            } else {
                $setter($entity, (is_array($value) ? array_first($value) : $value));
            }
        }
    }

    /**
     * @param array<object> $entities
     * @return array<object>
     * @throws Throwable
     */
    protected function applyRelations(array $entities): array
    {
        if ($this instanceof SupportsRelations && !empty($this->with)) {
            $relationTree = $this->buildRelationTree($this->with);
            $this->loadRelationsTree($entities, $relationTree);
        }

        return $entities;
    }

    /**
     * @param array $relations
     * @return array
     */
    private function buildRelationTree(array $relations): array
    {
        $tree = [];

        foreach ($relations as $relation) {
            $parts = explode('.', $relation);
            $current = &$tree;

            foreach ($parts as $part) {
                if (!isset($current[$part])) {
                    $current[$part] = [];
                }

                $current = &$current[$part];
            }
        }

        return $tree;
    }

    /**
     * @param array<object> $entities
     * @param array<string, array> $relationTree
     * @throws Throwable
     */
    private function loadRelationsTree(array $entities, array $relationTree): void
    {
        if (!$this instanceof SupportsRelations) {
            return;
        }

        foreach ($relationTree as $relation => $nested) {
            $descriptor = $this->loadRelation($relation);
            $descriptor->invokeLoader($entities);

            if ($nested) {
                $relatedEntities = [];

                foreach ($entities as $entity) {
                    $related = $descriptor->invokeAccessor($entity);

                    if (is_iterable($related)) {
                        foreach ($related as $r) {
                            $relatedEntities[] = $r;
                        }
                    } elseif ($related) {
                        $relatedEntities[] = $related;
                    }
                }

                if ($relatedEntities) {
                    $descriptor->repository->loadRelationsTree($relatedEntities, $nested);
                }
            }
        }
    }
}
