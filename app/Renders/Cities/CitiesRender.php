<?php

namespace App\Renders\Cities;

use App\Models\City;
use App\Models\Driver;
use App\Renders\AbstractRender;
use Throwable;

class CitiesRender extends AbstractRender
{
    /**
     * @param iterable<City> $cities
     * @return void
     * @throws Throwable
     */
    public function render(iterable $cities): void
    {
        $this->renderHeader('Cities');

        foreach ($cities as $city) {
            $this->padAuto([
                'ID:' => (string)$city->getId(),
                'Name:' => $city->getName(),
                'Count orders:' => $city->getOrders()?->count() ?? 0,
                'Sum orders:' => $city->getOrders()?->sum('sum') ?? 0,
                'Avg sum:' => $city->getOrders()?->avg('sum') ?? 0,
                'Count drivers' => $this->getCountDrivers($city),
            ]);

            $this->renderSeparator();
        }
    }

    /**
     * @param City $city
     * @return int
     */
    private function getCountDrivers(City $city): int
    {
        if (empty($city->getOrders())) {
            return 0;
        }

        $result = [];

        foreach ($city->getOrders() as $order) {
            if (!isset($result[$order->getDriverId()])) {
                $result[$order->getDriverId()] = true;
            }
        }

        return count($result);
    }
}
