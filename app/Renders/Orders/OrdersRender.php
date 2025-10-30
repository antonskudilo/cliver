<?php

namespace App\Renders\Orders;

use App\Models\Order;
use App\Renders\AbstractRender;

class OrdersRender extends AbstractRender
{
    /**
     * @param iterable<Order> $orders
     * @return void
     */
    public function render(iterable $orders): void
    {
        $this->renderHeader('Orders');

        foreach ($orders as $order) {
            $this->padAuto([
                'ID:' => (string)$order->getId(),
                'Date:' => $order->getDate()->format('Y-m-d'),
                'City:' => '#' . $order->getCityId() . ' ' . $order->getCity()?->getName(),
                'Driver:' => '#' . $order->getDriverId() . ' ' . $order->getDriver()?->getName(),
                'Sum:' => (string)$order->getSum(),
            ]);

            $this->renderSeparator();
        }
    }
}
