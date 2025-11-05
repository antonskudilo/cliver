<?php

namespace App\Repositories\Common\Support;

use App\Pivots\Common\PivotModel;

final class ManyToManyRelation extends Relation
{
    /**
     * @var PivotModel|string|null
     */
    protected PivotModel|string|null $pivot;

    /**
     * @var string
     */
    public string $relatedLocalKey;

    /**
     * @return string
     */
    public function getRelatedLocalKey(): string
    {
        return $this->relatedLocalKey;
    }

    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return true;
    }

    /**
     * @param string $name
     * @param string $relatedRepositoryClass
     * @param string $localKey
     * @param callable $foreignKeySelector
     * @param string $foreignKey
     * @param string $relatedKey
     * @param string $relatedLocalKey
     * @param callable $setter
     * @param callable $accessor
     * @param PivotModel|string $pivot
     * @return self
     */
    public static function makePivot(
        string $name,
        string $relatedRepositoryClass,
        string $localKey,
        callable $foreignKeySelector,
        string $foreignKey,
        string $relatedKey,
        string $relatedLocalKey,
        callable $setter,
        callable $accessor,
        PivotModel|string $pivot,
    ): self {
        $instance = new ManyToManyRelation(
            name: $name,
            relatedRepositoryClass: $relatedRepositoryClass,
            localKey: $localKey,
            foreignKey: $foreignKey,
            foreignKeySelector: $foreignKeySelector,
            relatedKey: $relatedKey,
            setter: $setter,
            accessor: $accessor
        );

        $instance->relatedLocalKey = $relatedLocalKey;
        $instance->pivot = $pivot;

        return $instance;
    }

    /**
     * @return PivotModel|string
     */
    public function getPivot(): PivotModel|string
    {
        return $this->pivot;
    }

    /**
     * @return bool
     */
    public function isPivotModel(): bool
    {
        return $this->pivot instanceof PivotModel;
    }

    /**
     * @return string
     */
    public function getPivotTableName(): string
    {
        if ($this->isPivotModel()) {
            return $this->pivot->getSourceName();
        }

        return $this->pivot;
    }
}