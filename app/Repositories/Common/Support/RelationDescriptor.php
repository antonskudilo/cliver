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
        public readonly string $name,
        public readonly BaseRepository $repository,
        public mixed $loader,
        public mixed $accessor,
        public RelationConfig $config,
        private readonly ?PivotLoader $pivotLoader
    ) {}

    /**
     * Фабричный метод для создания дескриптора с проверками типов.
     *
     * @param string $name
     * @param BaseRepository $repository
     * @param callable(array<object>, object): void $loader
     * @param callable $accessor
     * @param RelationConfig $config
     * @param PivotLoader $pivotLoader
     * @return self
     */
    public static function make(
        string         $name,
        BaseRepository $repository,
        callable       $loader,
        callable       $accessor,
        RelationConfig $config,
        PivotLoader    $pivotLoader
    ): self {
        // дополнительные проверки типов
        if (!is_callable($loader)) {
            throw new InvalidArgumentException("Loader for relation '$name' must be callable");
        }

        if (!is_callable($accessor)) {
            throw new InvalidArgumentException("Accessor for relation '$name' must be callable");
        }

        return new self($name, $repository, $loader, $accessor, $config, $pivotLoader);
    }

    /**
     * Выполняет загрузку отношения
     *
     * @param array<object> $entities
     * @return void
     */
    public function invokeLoader(array $entities): void
    {
        ($this->loader)($entities, $this->repository);
    }

    /**
     * Получает связанные сущности из родительского объекта
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
     * @return array<int|string> IDs главной модели, которые проходят фильтр по связанной
     * @throws Throwable
     */
    public function filterByRelation(array $conditions = []): array
    {
        // TODO: добавить в $conditions возможность передавать колбэк
        if ($this->config instanceof ManyToManyRelationConfig) {
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
     * TODO: добавить в $conditions возможность передавать колбэк
     * @throws Throwable
     */
    private function filterManyToMany(array $conditions): array
    {
        $related = $this->repository->where($conditions)->rows();

        if (empty($related)) {
            return [];
        }

        $relatedKey = $this->config->relatedLocalKey;
        $relatedIds = array_unique(array_map(fn($row) => $row[$relatedKey], $related));

        if (! $this->config instanceof ManyToManyRelationConfig) {
            throw new RuntimeException("PivotLoader is required for ManyToMany filtering");
        }

        $groupedPivots = $this->pivotLoader->load($this->config, $relatedIds, $this->config->relatedKey);

        if (!$groupedPivots) {
            return [];
        }

        $ownerIds = [];

        foreach ($groupedPivots as $pivots) {
            foreach ($pivots as $pivot) {
                // Pivot может быть массивом или моделью
                $ownerIds[] = is_array($pivot)
                    ? $pivot[$this->config->foreignKey]
                    : $pivot->getAttribute($this->config->foreignKey);
            }
        }

        return array_unique($ownerIds);
    }
}
