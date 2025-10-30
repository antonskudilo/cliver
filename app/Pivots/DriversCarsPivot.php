<?php

namespace App\Pivots;

use App\Pivots\Common\PivotModel;

class DriversCarsPivot extends PivotModel
{
    /**
     * @return string
     */
    public function getSourceName(): string
    {
        return 'drivers_cars';
    }

    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->getAttribute('date');
    }
}