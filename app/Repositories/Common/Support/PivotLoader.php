<?php

namespace App\Repositories\Common\Support;

use App\DataSource\DataSourceManager;
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
     * @param ManyToManyRelation $config
     * @param array<int|string> $foreignIds
     * @param null $foreignKey
     * @return array<int|string, PivotModel[]>
     */
    public function load(ManyToManyRelation $config, array $foreignIds, $foreignKey = null): array
    {
        // добавить в конфиг условия wherePivot


        if (!$foreignIds) {
            return [];
        }

        if (!isset($foreignKey)) {
            $foreignKey = $config->getForeignKey();
        }

        $dataSource = $this->dataSourceManager->getSourceFor($config->getPivotTableName());

        $rows = $dataSource->get(
            $config->getPivotTableName(),
            [
                $foreignKey => [
                    [
                        'operator' => 'in',
                        'value' => $foreignIds,
                    ]
                ],
            ]);

        $grouped = [];

        foreach ($rows as $row) {
            $fk = $row[$config->getForeignKey()];

            if ($config->isPivotModel()) {
                $grouped[$fk][] = new ($config->getPivot()::class)($row);
            } else {
                $grouped[$fk][] = $row;
            }

        }

        return $grouped;
    }
}