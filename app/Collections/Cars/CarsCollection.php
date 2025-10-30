<?php

namespace App\Collections\Cars;

use App\Collections\Common\BaseCollection;
use App\Models\Car;

/**
 * @extends BaseCollection<Car>
 * @method void addRow(Car $row)
 */
class CarsCollection extends BaseCollection
{
    protected function getItemClass(): string
    {
        return Car::class;
    }
}
