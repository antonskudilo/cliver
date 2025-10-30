<?php

namespace App\Factories\Models\Cars;

use App\Exceptions\EntityCreationException;
use App\Factories\Models\Drivers\DriverFactoryInterface;
use App\Models\Car;
use Throwable;

readonly class CarFactory implements CarFactoryInterface
{
    /**
     * @param array $data
     * @return Car
     * @throws EntityCreationException
     */
    public function fromArray(array $data): Car
    {
        try {
            return new Car(
                (int)$data['id'],
                (string)$data['model'],
                (string)$data['number']
            );
        } catch (Throwable) {
            throw EntityCreationException::for(Car::class, $data);
        }
    }
}
