<?php

namespace App\Collections\Drivers;

use App\Collections\Common\BaseCollection;
use App\Models\Driver;

/**
 * @extends BaseCollection<Driver>
 * @method void addRow(Driver $row)
 */
class DriversCollection extends BaseCollection
{
    protected function getItemClass(): string
    {
        return Driver::class;
    }
}
