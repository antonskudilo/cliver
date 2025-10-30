<?php

namespace App\Renders\Drivers;

use App\Models\Driver;
use App\Renders\AbstractRender;
use Throwable;

class DriversRender extends AbstractRender
{
    /**
     * @param iterable<Driver> $drivers
     * @return void
     * @throws Throwable
     */
    public function render(iterable $drivers): void
    {
        $this->renderHeader('Drivers');

        foreach ($drivers as $driver) {
            $this->padAuto([
                'ID:' => (string)$driver->getId(),
                'Name:' => $driver->getName(),
                'Phone:' => $driver->getPhone(),
                'Count orders:' => $driver->getOrders()?->count() ?? 0,
                'Sum orders:' => $driver->getOrders()?->sum('sum') ?? 0,
                'Avg sum:' => $driver->getOrders()?->avg('sum') ?? 0,
                'Cities:' => $this->concatDriverCities($driver),
            ]);

            $this->renderSeparator();
        }
    }

    /**
     * @param Driver $driver
     * @return string
     */
    private function concatDriverCities(Driver $driver): string
    {
        $result = '';

        if (empty($driver->getOrders())) {
            return $result;
        }

        foreach ($driver->getOrders() as $order) {
            $result .= "#{$order->getCityId()} {$order->getCity()?->getName()}; ";
        }

        return $result;
    }
}
