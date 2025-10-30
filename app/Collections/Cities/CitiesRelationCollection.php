<?php

namespace App\Collections\Cities;

use App\Collections\Common\BaseCollection;
use App\Collections\Common\RelationCollection;
use App\Models\City;

/**
 * @extends BaseCollection<City>
 * @method void addRow(City $row)
 */
class CitiesRelationCollection extends RelationCollection
{
    protected function getItemClass(): string
    {
        return City::class;
    }
}