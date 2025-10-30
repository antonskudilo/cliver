<?php

namespace App\Models;

use App\Collections\Orders\OrdersCollection;
use App\Models\Common\BaseModel;

class City extends BaseModel
{
    /**
     * @var OrdersCollection|null
     */
    private ?OrdersCollection $orders = null;

    public function __construct(
        private readonly int    $id,
        private readonly string $name
    ) {}

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return OrdersCollection|null
     */
    public function getOrders(): ?OrdersCollection
    {
        return $this->orders;
    }

    /**
     * @param OrdersCollection $orders
     * @return void
     */
    public function setOrders(OrdersCollection $orders): void
    {
        $this->orders = $orders;
    }
}
