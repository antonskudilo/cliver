<?php

namespace App\Collections\Orders;

use App\Collections\Common\BaseCollection;
use App\Models\Order;

/**
 * @extends BaseCollection<Order>
 * @method void addRow(Order $row)
 */
class OrdersCollection extends BaseCollection
{
    /**
     * @return string
     */
    protected function getItemClass(): string
    {
        return Order::class;
    }
}
