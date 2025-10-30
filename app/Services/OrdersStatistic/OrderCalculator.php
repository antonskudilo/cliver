<?php

namespace App\Services\OrdersStatistic;

use App\Models\Order;

class OrderCalculator implements OrderCalculatorInterface
{
    /**
     * @param Order $order
     * @return int
     */
    public function getSum(Order $order): int
    {
        return $order->getSum();
    }
}
