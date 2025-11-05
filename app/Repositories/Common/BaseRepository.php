<?php

namespace App\Repositories\Common;

use App\DataSource\DataSourceInterface;
use App\DataSource\DataSourceManager;
use App\Enums\SortDirectionEnum;
use App\Providers\AppResolver;
use App\Repositories\Common\Support\Relation;
use App\Repositories\Common\Support\RelationDescriptor;
use App\Repositories\Common\Support\RelationDescriptorFactory;
use InvalidArgumentException;
use LogicException;
use Throwable;

/**
 * @template TModel
 * @template TRepo of BaseRepository<TModel>
 */
abstract class BaseRepository
{
    /**
     * @use BaseRelationLoaderTrait<TModel, TRepo>
     */
    use BaseRelationLoaderTrait;

    /**
     * @var array<callable>
     */
    private array $conditions = [];

    /**
     * @var null|int
     */
    private ?int $limit = null;

    /**
     * @var int
     */
    private int $offset = 0;

    /**
     * @var array
     */
    private array $orderBy = [];

    /**
     * @var RelationDescriptorFactory
     */
    protected readonly RelationDescriptorFactory $relationFactory;

    /**
     * @var DataSourceInterface
     */
    private DataSourceInterface $source;

    /**
     * @throws Throwable
     */
    public function __construct(protected AppResolver $resolver)
    {
        $manager = $this->resolver->make(DataSourceManager::class);
        $this->source = $manager->getSourceFor($this->sourceName());
        $this->relationFactory = $this->resolver->make(RelationDescriptorFactory::class);
    }

    /**
     * @return string
     */
    abstract protected function sourceName(): string;

    /**
     * @param array $row
     * @return TModel
     */
    abstract protected function mapRow(array $row): mixed;

    /**
     * @param int $limit
     * @return TRepo
     */
    public function limit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $offset
     * @return TRepo
     */
    public function offset(int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @param string $orderBy
     * @param SortDirectionEnum $direction
     * @return TRepo
     */
    public function orderBy(string $orderBy, SortDirectionEnum $direction = SortDirectionEnum::DESC)
    {
        $this->orderBy[$orderBy] = $direction->value;

        return $this;
    }

    /**
     * @return array<TModel>
     * @throws Throwable
     */
    public function get(): array
    {
        $entities = [];

        foreach ($this->loadRows() as $row) {
            $entities[] = $this->mapRow($row);
        }

        $entities = $this->applyRelations($entities);
        $this->clearQueryParams();

        return $entities;
    }

    /**
     * @return array
     */
    public function rows(): array
    {
        $rows = [];

        foreach ($this->loadRows() as $row) {
            $rows[] = $row;
        }

        $this->clearQueryParams();

        return $rows;
    }

    /**
     * @return iterable<TModel>
     */
    private function loadRows(): iterable
    {
        return yield from $this->source->get(
            table: $this->sourceName(),
            conditions: $this->conditions,
            limit: $this->limit,
            offset: $this->offset,
            orderBy: $this->orderBy
        );
    }

    /**
     * @param int|string $id
     * @param string $keyField
     * @return TModel|null
     * @throws Throwable
     */
    public function find(int|string $id, string $keyField = 'id'): mixed
    {
        return $this->whereIs($keyField, $id)->first();
    }

    /**
     * @return mixed
     * @throws Throwable
     */
    private function first(): mixed
    {
        $rows = $this->limit(1)->get();

        if (empty($rows)) {
            return null;
        }

        return array_first($rows);
    }

    /**
     * @param array<int|string> $ids
     * @param string $keyField
     * @return array<int|string, TModel>
     * @throws Throwable
     */
    public function findManyGrouped(array $ids, string $keyField = 'id'): array
    {
        $entities = [];

        foreach ($this->whereIn($keyField, $ids)->loadRows() as $row) {
            $entities[$row[$keyField]][] = $this->mapRow($row);
        }

        $entities = $this->applyRelations($entities);
        $this->clearQueryParams();

        return $entities;
    }

    /**
     * @param int $batchSize
     * @return iterable<TModel>
     * @throws Throwable
     */
    public function each(int $batchSize = 100): iterable
    {
        $this->offset = 0;
        $this->limit = $batchSize;

        while (true) {
            $entities = [];

            foreach ($this->loadRows() as $row) {
                $entities[] = $this->mapRow($row);
            }

            if (empty($entities)) {
                break;
            }

            $entities = $this->applyRelations($entities);

            yield from $entities;

            $this->offset += $batchSize;
        }

        $this->clearQueryParams();
    }

    /**
     * @param int $chunkSize
     * @param callable(array<TModel>): void $callback
     * @return void
     * @throws Throwable
     */
    public function chunk(int $chunkSize, callable $callback): void
    {
        while (true) {
            $entities = [];

            foreach ($this->loadRows() as $row) {
                $entities[] = $this->mapRow($row);
            }

            if (empty($entities)) {
                break;
            }

            $entities = $this->applyRelations($entities);
            $callback($entities);
            $this->offset += $chunkSize;
        }

        $this->clearQueryParams();
    }

    /**
     * @return void
     */
    private function clearQueryParams(): void
    {
        $this->unsetLimit();
        $this->unsetOffset();
        $this->unsetConditions();
        $this->unsetOrderBy();
        $this->unsetRelations();
    }

    /**
     * @return void
     */
    private function unsetLimit(): void
    {
        $this->limit = null;
    }

    /**
     * @return void
     */
    private function unsetOffset(): void
    {
        $this->offset = 0;
    }

    /**
     * @return void
     */
    private function unsetConditions(): void
    {
        $this->conditions = [];
    }

    /**
     * @return void
     */
    private function unsetOrderBy(): void
    {
        $this->orderBy = [];
    }

    /**
     * @param bool $condition
     * @param callable(TRepo): TRepo $whenTrue
     * @param callable(TRepo): TRepo|null $whenFalse
     * @return TRepo
     */
    public function if(bool $condition, callable $whenTrue, ?callable $whenFalse = null): mixed
    {
        if ($condition) {
            return $whenTrue($this);
        }

        if (isset($whenFalse)) {
            return $whenFalse($this);
        }

        return $this;
    }

    /**
     * @param array $conditions
     * @return TRepo
     */
    public function where(array $conditions)
    {
        foreach ($conditions as $field => $condition) {
            $this->addCondition($field, $condition);
        }

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return static
     */
    public function whereNot(string $field, mixed $value): static
    {
        return $this->addCondition($field, ['!=', $value]);
    }

    /**
     * @param string $field
     * @param int|string $value
     * @return static
     */
    public function whereIs(string $field, int|string $value): static
    {
        return $this->addCondition($field, ['=', $value]);
    }

    /**
     * @param string $field
     * @param array $values
     * @return static
     */
    public function whereIn(string $field, array $values): static
    {
        return $this->addCondition($field, ['in', $values]);
    }

    /**
     * @param string $field
     * @param array $values
     * @return static
     */
    public function whereNotIn(string $field, array $values): static
    {
        return $this->addCondition($field, ['not in', $values]);
    }

    /**
     * @param string $field
     * @param string $needle
     * @return static
     */
    public function whereContains(string $field, string $needle): static
    {
        return $this->addCondition($field, ['like', "%$needle%"]);
    }

    /**
     * @param string $field
     * @param int|float $value
     * @return static
     */
    public function whereGt(string $field, int|float $value): static
    {
        return $this->addCondition($field, ['>', $value]);
    }

    /**
     * @param string $field
     * @param int|float $value
     * @return static
     */
    public function whereGte(string $field, int|float $value): static
    {
        return $this->addCondition($field, ['>=', $value]);
    }

    /**
     * @param string $field
     * @param int|float $value
     * @return static
     */
    public function whereLt(string $field, int|float $value): static
    {
        return $this->addCondition($field, ['<', $value]);
    }

    /**
     * @param string $field
     * @param int|float $value
     * @return static
     */
    public function whereLte(string $field, int|float $value): static
    {
        return $this->addCondition($field, ['<=', $value]);
    }

    /**
     * @param string $field
     * @param array|string|int|float $value
     * @return static
     */
    protected function addCondition(string $field, array|string|int|float $value): static
    {
        if (is_array($value) && isset($value[0]) && array_key_exists(1, $value)) {
            $operator = strtolower(trim($value[0]));
            $val = $value[1];
        } else {
            $operator = '=';
            $val = is_array($value) ? $value[0] : $value;
        }

        $this->conditions[$field][] = [
            'operator' => $operator,
            'value' => $val,
        ];

        return $this;
    }

    /**
     * @param string $relation
     * @param array<string, mixed>|callable $conditions
     * @return static
     * @throws Throwable
     */
    public function whereHas(string $relation, array|callable $conditions): static
    {
        $relationConfig = $this->getRelationConfig($relation);
        $descriptor = $this->loadRelation($relation);

        if (is_callable($conditions)) {
            $relatedRepo = $descriptor->repository;
            $conditions($relatedRepo);
            $ids = $descriptor->filterByRelation();
        } else {
            $ids = $descriptor->filterByRelation($conditions);
        }

        return $this->whereIn($relationConfig->relatedLocalKey ?? $relationConfig->localKey, $ids);
    }

    /**
     * @param string $relation
     * @return Relation|null
     * @throws Throwable
     */
    protected function getRelationConfig(string $relation): ?Relation
    {
        if (!$this instanceof SupportsRelations) {
            throw new LogicException(sprintf(
                'Repository %s does not support relations',
                static::class
            ));
        }

        $map = $this->getRelationMap();

        if (
            !isset($map[$relation])
            || !$map[$relation] instanceof Relation
        ) {
            throw new InvalidArgumentException("Unknown relation: $relation");
        }

        return $map[$relation];
    }

    /**
     * @throws Throwable
     */
    public function loadRelation(string $relation): RelationDescriptor
    {
        if (!isset($this->relationFactory)) {
            throw new LogicException(sprintf(
                "Cannot load relation '%s': RelationDescriptorFactory is not initialized in repository %s",
                $relation,
                static::class
            ));
        }

        return $this->relationFactory->make(
            $this->getRelationConfig($relation)
        );
    }
}
