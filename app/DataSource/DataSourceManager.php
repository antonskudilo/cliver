<?php

namespace App\DataSource;

final readonly class DataSourceManager
{
    public function __construct(private array $config, private DataSourceInterface $defaultDataSource) {}

    /**
     */
    public function getSourceFor(string $entity): DataSourceInterface
    {
        if (isset($this->config[$entity])) {
            return $this->config[$entity];
        }

        return $this->defaultDataSource;
    }
}
