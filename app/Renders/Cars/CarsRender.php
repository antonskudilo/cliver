<?php

namespace App\Renders\Cars;

use App\Models\Car;
use App\Renders\AbstractRender;
use Throwable;

class CarsRender extends AbstractRender
{
    /**
     * @param iterable<Car> $cars
     * @return void
     * @throws Throwable
     */
    public function render(iterable $cars): void
    {
        $this->renderHeader('Cars');

        foreach ($cars as $car) {
            $this->padAuto([
                'ID:' => (string)$car->getId(),
                'Model:' => $car->getModel(),
                'Number:' => $car->getNumber(),
                'Drivers:' => $this->concatCarDrivers($car),
            ]);

            $this->renderSeparator();
        }
    }

    /**
     * @param Car $car
     * @return string
     */
    private function concatCarDrivers(Car $car): string
    {
        $result = '';

        if (empty($car->getDrivers())) {
            return $result;
        }

        $drivers = $car->getDrivers();

        foreach ($car->getDrivers() as $driver) {
            $pivot = $drivers->getPivot($driver);
            $result .= "#{$driver->getId()} {$driver->getName()} from {$pivot->getDate()}; ";
        }

        return $result;
    }
}
