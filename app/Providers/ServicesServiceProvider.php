<?php

namespace App\Providers;

use App\Services\OrdersStatistic\OrderCalculator;
use App\Services\OrdersStatistic\OrderCalculatorInterface;
use Cliver\Core\Core\Container;
use Cliver\Core\Providers\ServiceProviderInterface;

readonly final class ServicesServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        $services = [
            OrderCalculatorInterface::class => OrderCalculator::class,
        ];

        foreach ($services as $abstract => $concrete) {
            $container->bind($abstract, $concrete);
        }
    }
}
