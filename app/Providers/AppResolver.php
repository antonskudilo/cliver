<?php

namespace App\Providers;

use Cliver\Core\Core\Container;
use Cliver\Core\Exceptions\ServiceNotFoundException;
use Throwable;

final readonly class AppResolver
{
    public function __construct(
        private Container $container
    ) {}

    /**
     * Resolve a service from the container with an optional fallback.
     *
     * @template T
     * @param class-string<T> $name Class or interface name to resolve
     * @param class-string<T> $default Optional fallback value or factory
     * @return T
     * @throws Throwable
     */
    public function make(string $name, mixed $default = null): mixed
    {
        try {
            return $this->container->get($name);
        } catch (ServiceNotFoundException) {
            if (isset($default)) {
                return $this->container->get($default);
            }

            throw new ServiceNotFoundException($name);
        }
    }
}
