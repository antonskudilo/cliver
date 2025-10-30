<?php

namespace App\Factories\Models\Drivers;

use App\Exceptions\EntityCreationException;
use App\Models\Driver;
use Throwable;

readonly class DriverFactory implements DriverFactoryInterface
{
    /**
     * @param array $data
     * @return Driver
     * @throws EntityCreationException
     */
    public function fromArray(array $data): Driver
    {
        try {
            return new Driver(
                (int)$data['id'],
                (string)$data['name'],
                (string)$data['phone']
            );
        } catch (Throwable) {
            throw EntityCreationException::for(Driver::class, $data);
        }
    }
}
