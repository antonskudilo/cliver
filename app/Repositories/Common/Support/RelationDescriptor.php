<?php

namespace App\Repositories\Common\Support;

use App\Repositories\Common\BaseRepository;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class RelationDescriptor
{
    /**
     * @param callable(array<object>, object): void $loader
     * @param callable(object): iterable<object>|object|null $accessor
     */
    private function __construct(
        public readonly string         $name,
        public readonly BaseRepository $repository,
        public mixed                   $loader,
        public mixed                   $accessor,
        public Relation                $config,
        private readonly ?PivotLoader  $pivotLoader
    ) {}

    /**
     * Factory method for creating a descriptor with type checks
     *
     * @param string $name
     * @param BaseRepository $repository
     * @param callable(array<object>, object): void $loader
     * @param callable $accessor
     * @param Relation $relation
     * @param PivotLoader $pivotLoader
     * @return self
     */
    public static function make(
        string         $name,
        BaseRepository $repository,
        callable       $loader,
        callable       $accessor,
        Relation       $relation,
        PivotLoader    $pivotLoader
    ): self {
        if (!is_callable($loader)) {
            throw new InvalidArgumentException("Loader for relation '$name' must be callable");
        }

        if (!is_callable($accessor)) {
            throw new InvalidArgumentException("Accessor for relation '$name' must be callable");
        }

        return new self($name, $repository, $loader, $accessor, $relation, $pivotLoader);
    }

    /**
     * Performs relation loading
     *
     * @param array<object> $entities
     * @return void
     */
    public function invokeLoader(array $entities): void
    {
        ($this->loader)($entities, $this->repository);
    }

    /**
     * Retrieves related entities from the parent object
     *
     * @param object $entity
     * @return iterable<object>|object|null
     */
    public function invokeAccessor(object $entity): iterable|object|null
    {
        return ($this->accessor)($entity);
    }

    /**
     * @param array<string, mixed> $conditions
     * @return array<int|string>
     * @throws Throwable
     */
    public function filterByRelation(array $conditions = []): array
    {
        // TODO: добавить в $conditions возможность передавать колбэк
        if ($this->config instanceof ManyToManyRelation) {
            return $this->filterManyToMany($conditions);
        }

        return $this->filterDirect($conditions);
    }

    /**
     * @param array $conditions
     * @return array
     * @throws Throwable
     */
    private function filterDirect(array $conditions): array
    {
        $related = $this->repository->where($conditions)->rows();

        if (empty($related)) {
            return [];
        }

        $relatedKey = $this->config->relatedKey;

        return array_unique(array_map(fn($row) => $row[$relatedKey], $related));
    }

    /**
     * @throws Throwable
     */
    private function filterManyToMany(array $conditions): array
    {
        if (!$this->config instanceof ManyToManyRelation) {
            throw new RuntimeException("PivotLoader is required for ManyToMany filtering");
        }

        $pivotConditions = $this->repository->getPivotConditions();
        $related = $this->repository->where($conditions)->rows();

        if (empty($related)) {
            return [];
        }

        $relatedKey = $this->config->relatedLocalKey;
        $relatedIds = array_unique(array_map(fn($row) => $row[$relatedKey], $related));

        $groupedPivots = $this->pivotLoader->load(
            $this->config,
            $relatedIds,
            $this->config->relatedKey,
            $pivotConditions
        );

        if (!$groupedPivots) {
            return [];
        }

        $ownerIds = [];

        foreach ($groupedPivots as $pivots) {
            foreach ($pivots as $pivot) {
                $ownerIds[] = is_array($pivot)
                    ? $pivot[$this->config->foreignKey]
                    : $pivot->getAttribute($this->config->foreignKey);
            }
        }

        return array_unique($ownerIds);
    }
}
