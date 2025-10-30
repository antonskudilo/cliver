<?php

namespace App\Factories\Models\Orders;

use App\Exceptions\EntityCreationException;
use App\Models\Order;
use DateTimeImmutable;
use Exception;
use Throwable;

readonly class OrderFactory implements OrderFactoryInterface
{
    /**
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function fromArray(array $data): Order
    {
        try {
            return new Order(
                (int)$data['id'],
                (int)$data['city_id'],
                (int)$data['driver_id'],
                (int)$data['sum'],
                new DateTimeImmutable($data['date']),
            );
        } catch (Throwable) {
            throw EntityCreationException::for(Order::class, $data);
        }
    }
}
