<?php

namespace App\Repositories\Common\Support;

use App\Repositories\Common\BaseRepository;
use Throwable;

final readonly class PivotRelationLoader
{
    public function __construct(private PivotLoader $pivotLoader) {}

    /**
     * @param array<object> $entities
     * @param ManyToManyRelationConfig $config
     * @param BaseRepository $relatedRepo
     * @throws Throwable
     */
    public function loadManyToMany(array $entities, ManyToManyRelationConfig $config, BaseRepository $relatedRepo): void
    {
        $relatedKey = $config->relatedKey;
        $ids = array_map($config->foreignKeySelector, $entities);
        $pivotGroups = $this->pivotLoader->load($config, $ids);
        $relatedIds = [];

        foreach ($pivotGroups as $pivots) {
            foreach ($pivots as $pivot) {
                $relatedIds[] = $pivot->getAttribute($relatedKey);
            }
        }

        $relatedModels = $relatedRepo->findManyGrouped(
            array_unique($relatedIds),
            $config->getRelatedLocalKey()
        );

        foreach ($entities as $entity) {
            $entityForeignKey = ($config->foreignKeySelector)($entity);
            $pivots = $pivotGroups[$entityForeignKey] ?? [];
            $related = [];

            foreach ($pivots as $pivot) {
                $relatedEntityId = $config->isPivotModel()
                    ? $pivot->getAttribute($relatedKey)
                    : $pivot[$relatedKey];

                $relatedEntity = array_first($relatedModels[$relatedEntityId]);

                if ($relatedEntity) {
                    $related[] = $relatedEntity;
                }
            }

            if ($config->isPivotModel()) {
                ($config->setter)($entity, $related, $pivots);
            } else {
                ($config->setter)($entity, $related);
            }
        }
    }
}