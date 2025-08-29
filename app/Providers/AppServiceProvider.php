<?php

namespace App\Providers;

use Cliver\Core\Core\Container;
use Cliver\Core\Providers\ServiceProviderInterface;

readonly final class AppServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        //
    }
}
