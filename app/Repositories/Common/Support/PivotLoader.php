<?php

namespace App\Repositories\Common\Support;

use App\DataSource\DataSourceInterface;
use App\Pivots\Common\PivotModel;

final readonly class PivotLoader
{
    public function __construct(private DataSourceInterface $dataSource) {}

    /**
     * @param ManyToManyRelationConfig $config
     * @param array<int|string> $foreignIds
     * @param null $foreignKey
     * @return array<int|string, PivotModel[]>
     */
    public function load(ManyToManyRelationConfig $config, array $foreignIds, $foreignKey = null): array
    {
        if (!$foreignIds) {
            return [];
        }

        if (!isset($foreignKey)) {
            $foreignKey = $config->getForeignKey();
        }

        $rows = $this->dataSource->get($config->getPivotTableName(), [$foreignKey => $foreignIds]);
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