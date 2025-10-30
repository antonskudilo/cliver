<?php

namespace App\Providers;

use Cliver\Core\Core\Container;
use Cliver\Core\Providers\ServiceProviderInterface;
use Throwable;

readonly final class AppServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return void
     * @throws Throwable
     */
    public function register(Container $container): void
    {
        (new FactoriesServiceProvider())->register($container);
        (new DataSourcesServiceProvider())->register($container);
        (new ServicesServiceProvider())->register($container);
    }
}
