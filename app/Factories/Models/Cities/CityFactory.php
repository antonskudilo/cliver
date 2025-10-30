<?php

namespace App\Factories\Models\Cities;

use App\Exceptions\EntityCreationException;
use App\Models\City;
use Throwable;

class CityFactory implements CityFactoryInterface
{
    /**
     * @param array $data
     * @throws Throwable
     * @return City
     */
    public function fromArray(array $data): City
    {
        try {
            return new City(
                (int)$data['id'],
                (string)$data['name'],
            );
        } catch (Throwable) {
            throw EntityCreationException::for(City::class, $data);
        }
    }
}
