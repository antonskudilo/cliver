<?php

namespace App\Repositories\Common\Support;

use App\Providers\AppResolver;
use App\Repositories\Common\BaseRepository;
use InvalidArgumentException;
use Throwable;

final readonly class RelationDescriptorFactory
{
    public function __construct(
        private AppResolver $resolver,
        private PivotLoader $pivotLoader
    ) {}

    /**
     * @template TEntity of object
     * @template TRelated of object
     * @param RelationConfig $config
     * @return RelationDescriptor
     * @throws Throwable
     */
    public function make(RelationConfig $config): RelationDescriptor
    {
        $relatedRepository = $this->resolver->make($config->relatedRepositoryClass);

        if (!$relatedRepository instanceof BaseRepository) {
            throw new InvalidArgumentException("Repository for relation '$config->name' must be instance of BaseRepository");
        }

        $loader = match (true) {
            $config instanceof HasManyRelationConfig => $this->makeHasManyLoader($config, $relatedRepository),
            $config instanceof HasOneRelationConfig  => $this->makeHasOneLoader($config, $relatedRepository),
            $config instanceof ManyToManyRelationConfig   => $this->makePivotLoader($config),
            default => throw new InvalidArgumentException("Unsupported relation type: " . get_class($config))
        };

        return RelationDescriptor::make(
            name: $config->name,
            repository: $relatedRepository,
            loader: $loader,
            accessor: $config->accessor,
            config: $config,
            pivotLoader: $this->pivotLoader
        );
    }

    /**
     * @param HasManyRelationConfig $config
     * @param BaseRepository $relatedRepository
     * @return callable
     */
    private function makeHasManyLoader(HasManyRelationConfig $config, BaseRepository $relatedRepository): callable
    {
        return function (array $entities) use ($config, $relatedRepository): void {
            $relatedRepository->eagerLoad(
                $entities,
                $config->foreignKeySelector,
                fn(array $ids) => $relatedRepository->findManyGrouped($ids, $config->relatedKey),
                $config->setter,
                true
            );
        };
    }

    /**
     * @param HasOneRelationConfig $config
     * @param BaseRepository $relatedRepository
     * @return callable
     */
    private function makeHasOneLoader(HasOneRelationConfig $config, BaseRepository $relatedRepository): callable
    {
        return function (array $entities) use ($config, $relatedRepository): void {
            $relatedRepository->eagerLoad(
                $entities,
                $config->foreignKeySelector,
                fn(array $ids) => $relatedRepository->findManyGrouped($ids, $config->relatedKey),
                $config->setter,
                false
            );
        };
    }

    /**
     * @param ManyToManyRelationConfig $config
     * @return callable
     */
    private function makePivotLoader(ManyToManyRelationConfig $config): callable
    {
        return fn(array $entities, BaseRepository $relatedRepository) =>
        (new PivotRelationLoader($this->pivotLoader))->loadManyToMany($entities, $config, $relatedRepository);
    }
}