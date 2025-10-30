<?php

namespace App\Providers;

use App\DataSource\DataSourceInterface;
use App\DataSource\CsvDataSource;
use App\DataSource\DatabaseDataSource;
use App\Factories\PdoFactory;
use Cliver\Core\Core\Container;
use Cliver\Core\Providers\ServiceProviderInterface;
use RuntimeException;

readonly final class DataSourcesServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        $connection = config('datasource.connection');

        switch ($connection) {
            case 'db':
                $container->bind(DataSourceInterface::class, new DatabaseDataSource(PdoFactory::make(
                    host: config('datasource.db.host'),
                    port: config('datasource.db.port'),
                    database: config('datasource.db.database'),
                    user: config('datasource.db.user'),
                    password: config('datasource.db.password'),
                    charset: config('datasource.db.charset')
                )));
                break;
            case 'csv':
                $container->bind(DataSourceInterface::class, new CsvDataSource(
                    config('datasource.csv.path')
                ));
                break;
            default:
                throw new RuntimeException('Unsupported DB_CONNECTION');
        }
    }
}
