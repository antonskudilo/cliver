<?php

namespace App\Renders\OrdersStatistic;

use App\Renders\AbstractRender;
use App\Services\OrdersStatistic\OrdersStatistic;

class OrdersStatisticRender extends AbstractRender
{
    /**
     * @param OrdersStatistic $statistic
     * @return void
     */
    public function render(OrdersStatistic $statistic): void
    {
        $this->renderHeader('Orders statistics');

        $this->padAuto([
            'Total orders:' => (string)$statistic->getQuantity(),
            'Total sum' => (string)$statistic->getSum(),
            'Average sum:' => (string)$statistic->getAverage(),
        ]);
    }
}
