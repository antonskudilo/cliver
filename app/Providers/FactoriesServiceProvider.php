<?php

namespace App\Providers;

use App\Factories\Models\Cars\CarFactory;
use App\Factories\Models\Cars\CarFactoryInterface;
use App\Factories\Models\Cities\CityFactory;
use App\Factories\Models\Cities\CityFactoryInterface;
use App\Factories\Models\Drivers\DriverFactory;
use App\Factories\Models\Drivers\DriverFactoryInterface;
use App\Factories\Models\Orders\OrderFactory;
use App\Factories\Models\Orders\OrderFactoryInterface;
use Cliver\Core\Core\Container;
use Cliver\Core\Providers\ServiceProviderInterface;

readonly final class FactoriesServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        $factories = [
            CityFactoryInterface::class => CityFactory::class,
            DriverFactoryInterface::class => DriverFactory::class,
            OrderFactoryInterface::class => OrderFactory::class,
            CarFactoryInterface::class => CarFactory::class,
        ];

        foreach ($factories as $abstract => $concrete) {
            $container->bind($abstract, $concrete);
        }
    }
}
