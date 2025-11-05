<?php

namespace App\Providers;

use App\DataSource\CompositeDataSource;
use App\DataSource\CsvDataSource;
use App\DataSource\DatabaseDataSource;
use App\DataSource\DataSourceInterface;
use App\DataSource\DataSourceManager;
use App\Factories\PdoFactory;
use Cliver\Core\Core\Container;
use Cliver\Core\Providers\ServiceProviderInterface;
use RuntimeException;

final class DataSourcesServiceProvider implements ServiceProviderInterface
{
    private static ?DatabaseDataSource $dbSource = null;
    private static ?CsvDataSource $csvSource = null;

    /**
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        $container->singleton(DataSourceManager::class, function() use ($container) {
            $connection = config('datasource.connection', 'csv');

            $defaultDataSource = match ($connection) {
                'db'  => self::$dbSource ??= $this->createDbSource(),
                'csv' => self::$csvSource ??= $this->createCsvSource(),
                default => throw new RuntimeException('Unsupported DB_CONNECTION'),
            };

            $entitySourcesConfig = include config_path('entity_sources.php');
            $resolvedConfig = $this->resolveEntitySources($entitySourcesConfig);

            return new DataSourceManager($resolvedConfig, $defaultDataSource);
        });
    }

    /**
     * @param array<string, array<string|object>> $entitySourcesConfig
     * @return array<string, CompositeDataSource>
     */
    private function resolveEntitySources(array $entitySourcesConfig): array
    {
        $resolvedConfig = [];

        foreach ($entitySourcesConfig as $entity => $sources) {
            $resolvedConfig[$entity] = $this->buildCompositeForEntity($sources);
        }

        return $resolvedConfig;
    }

    /**
     * @param array<string|DataSourceInterface> $sources
     * @return CompositeDataSource
     */
    private function buildCompositeForEntity(array $sources): CompositeDataSource
    {
        $resolved = [];

        foreach ($sources as $source) {
            $resolved[] = $this->resolveSingleSource($source);
        }

        return new CompositeDataSource($resolved);
    }

    /**
     * @param string|DataSourceInterface $source
     * @return DataSourceInterface
     */
    private function resolveSingleSource(string|DataSourceInterface $source): DataSourceInterface
    {
        if (is_string($source)) {
            return match ($source) {
                DatabaseDataSource::class => self::$dbSource ??= $this->createDbSource(),
                CsvDataSource::class => self::$csvSource ??= $this->createCsvSource(),
                default => throw new RuntimeException("Unsupported DataSource: $source"),
            };
        }

        if ($source instanceof DataSourceInterface) {
            return $source;
        }

        throw new RuntimeException('Unsupported DataSource type');
    }

    /**
     * @return DatabaseDataSource
     */
    private static function createDbSource(): DatabaseDataSource
    {
        return new DatabaseDataSource(
            PdoFactory::make(
                host: config('datasource.db.host'),
                port: config('datasource.db.port'),
                database: config('datasource.db.database'),
                user: config('datasource.db.user'),
                password: config('datasource.db.password'),
                charset: config('datasource.db.charset')
            )
        );
    }

    /**
     * @return CsvDataSource
     */
    private static function createCsvSource(): CsvDataSource
    {
        return new CsvDataSource(config('datasource.csv.path'));
    }
}
