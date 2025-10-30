<?php

namespace App\Collections\Cities;

use App\Collections\Common\BaseCollection;
use App\Models\City;

/**
 * @extends BaseCollection<City>
 * @method void addRow(City $row)
 */
class CitiesCollection extends BaseCollection
{
    protected function getItemClass(): string
    {
        return City::class;
    }
}