<?php

namespace App\Repositories\Common\Support;

use App\DataSource\DataSourceManager;
use App\Enums\ComparisonOperatorEnum;
use App\Pivots\Common\PivotModel;
use App\Providers\AppResolver;
use Throwable;

final class PivotLoader
{
    /**
     * @var DataSourceManager
     */
    private mixed $dataSourceManager;

    /**
     * @throws Throwable
     */
    public function __construct(protected AppResolver $resolver) {
        $this->dataSourceManager = $this->resolver->make(DataSourceManager::class);
    }

    /**
     * @param ManyToManyRelation $relation
     * @param array<int|string> $foreignIds
     * @param null $foreignKey
     * @param array $pivotConditions
     * @return array<int|string, PivotModel[]>
     */
    public function load(ManyToManyRelation $relation, array $foreignIds, $foreignKey = null, array $pivotConditions = []): array
    {
        if (!$foreignIds) {
            return [];
        }

        if (!isset($foreignKey)) {
            $foreignKey = $relation->getForeignKey();
        }

        $dataSource = $this->dataSourceManager->getSourceFor($relation->getPivotTableName());

        $conditions = [
            $foreignKey => [
                [
                    'operator' => ComparisonOperatorEnum::IN,
                    'value' => $foreignIds,
                ]
            ],
        ];

        foreach ($pivotConditions as $field => $rules) {
            $conditions[$field] = $rules;
        }

        $rows = $dataSource->get(
            $relation->getPivotTableName(),
            $conditions
        );

        $grouped = [];

        foreach ($rows as $row) {
            $fk = $row[$relation->getForeignKey()];

            if ($relation->isPivotModel()) {
                $grouped[$fk][] = new ($relation->getPivot()::class)($row);
            } else {
                $grouped[$fk][] = $row;
            }

        }

        return $grouped;
    }
}