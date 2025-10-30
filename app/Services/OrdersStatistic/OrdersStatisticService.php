<?php

namespace App\Services\OrdersStatistic;

use App\Requests\Orders\OrdersRequest;
use App\Services\Orders\OrdersService;
use Throwable;

final readonly class OrdersStatisticService
{
    public function __construct(
        private OrdersService             $service,
        private OrdersStatisticAggregator $aggregator,
    ) {}

    /**
     * @param OrdersRequest $request
     * @return OrdersStatistic
     * @throws Throwable
     */
    public function handle(OrdersRequest $request): OrdersStatistic
    {
        $orders = $this->service->fetchWithRelations($request);

        return $this->aggregator->aggregate($orders);
    }
}