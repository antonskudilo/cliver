<?php

namespace App\Services\OrdersStatistic;

use App\Models\Order;

interface OrderCalculatorInterface
{
    /**
     * @param Order $order
     * @return int
     */
    public function getSum(Order $order): int;
}
