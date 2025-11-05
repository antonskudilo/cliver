<?php

namespace App\Repositories\Common\Support;

abstract class Relation
{
    protected function __construct(
        public readonly string $name,
        public readonly string $relatedRepositoryClass,
        public readonly string $localKey,
        public readonly string $foreignKey,
        public readonly mixed  $foreignKeySelector,
        public readonly string $relatedKey,
        public readonly mixed  $setter,
        public readonly mixed  $accessor
    ) {}

    /**
     * @return string
     */
    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    /**
     * @return bool
     */
    abstract public function isCollection(): bool;

    /**
     * @template TEntity of object
     * @template TRelated of object
     *
     * @param string $name Имя отношения
     * @param string $relatedRepositoryClass
     * @param string $localKey
     * @param string $foreignKey
     * @param callable $foreignKeySelector Функция получения foreign key
     * @param string $relatedKey
     * @param callable(TEntity, TRelated|null): void $setter Функция установки связанной сущности
     * @param callable $accessor Функция получения связи из сущности
     * @return static
     */
    public static function make(
        string   $name,
        string   $relatedRepositoryClass,
        string   $localKey,
        string   $foreignKey,
        callable $foreignKeySelector,
        string   $relatedKey,
        callable $setter,
        callable $accessor
    ): static {
        return new static(
            name: $name,
            relatedRepositoryClass: $relatedRepositoryClass,
            localKey: $localKey,
            foreignKey: $foreignKey,
            foreignKeySelector: $foreignKeySelector,
            relatedKey: $relatedKey,
            setter: $setter,
            accessor: $accessor
        );
    }
}
