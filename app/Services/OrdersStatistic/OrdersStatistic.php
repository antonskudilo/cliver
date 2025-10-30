<?php

namespace App\Services\OrdersStatistic;

use App\Models\Order;

class OrdersStatistic
{
    /**
     * @param int $quantity
     * @param int $sum
     */
    public function __construct(
        private int $quantity = 0,
        private int $sum = 0,
    ) {}

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getSum(): int
    {
        return $this->sum;
    }

    /**
     * @return int
     */
    public function getAverage(): int
    {
        $quantity = $this->getQuantity();

        if (empty($quantity)) {
            return 0;
        }

        return (int)round($this->getSum() / $this->getQuantity());
    }

    /**
     * @param Order $order
     * @param OrderCalculatorInterface $service
     * @return void
     */
    public function addOrder(Order $order, OrderCalculatorInterface $service): void
    {
        $this->addQuantity();
        $this->addSum($service->getSum($order));
    }

    /**
     * @return void
     */
    public function addQuantity(): void
    {
        $this->quantity++;
    }

    /**
     * @param int $sum
     * @return void
     */
    public function addSum(int $sum): void
    {
        $this->sum += $sum;
    }
}
