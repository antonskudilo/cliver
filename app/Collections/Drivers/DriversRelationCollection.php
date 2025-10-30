<?php

namespace App\Collections\Drivers;

use App\Collections\Common\RelationCollection;
use App\Models\Driver;
use App\Pivots\DriversCarsPivot;

/**
 * @extends RelationCollection<Driver, DriversCarsPivot>
 * @method void addRow(Driver $row)
 */
class DriversRelationCollection extends RelationCollection
{
    protected function getItemClass(): string
    {
        return Driver::class;
    }
}
