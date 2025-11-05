<?php

namespace App\Console\Commands;

use App\Renders\Orders\OrdersRender;
use App\Requests\Orders\OrdersRequest;
use App\Services\Orders\OrdersService;
use Throwable;

readonly class OrdersCommand extends BaseCommand
{
    public function __construct(
        private OrdersService $service,
        private OrdersRender  $render,
    ) {}

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'orders';
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Show list of orders. Additional params: drivers, cities, city, driver, date.';
    }

    /**
     * @param array $args
     * @return void
     * @throws Throwable
     */
    public function execute(array $args): void
    {
        $filters = $this->parseFilters($args);

        $request = OrdersRequest::fromArray([
            'drivers' => $filters['drivers'] ?? null,
            'cities' => $filters['cities'] ?? null,
            'city' => $filters['city'] ?? null,
            'driver' => $filters['driver'] ?? null,
            'date' => $filters['date'] ?? null,
            'limit' => $filters['limit'] ?? null,
            'orderBy' => $filters['orderBy'] ?? null,
            'direction' => $filters['direction'] ?? null,
        ]);

        $orders = $this->service->fetchWithRelations($request);
        $this->render->render($orders);
    }
}
