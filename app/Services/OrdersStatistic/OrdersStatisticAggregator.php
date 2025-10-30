<?php

namespace App\Services\OrdersStatistic;

use App\Models\Order;

readonly class OrdersStatisticAggregator
{
    public function __construct(
        private OrderCalculatorInterface $calculator,
    ) {}

    /**
     * @param iterable<Order> $orders
     * @return OrdersStatistic
     */
    public function aggregate(iterable $orders): OrdersStatistic
    {
        $statistic = new OrdersStatistic();

        foreach ($orders as $order) {
            $statistic->addOrder($order, $this->calculator);
        }

        return $statistic;
    }
}
