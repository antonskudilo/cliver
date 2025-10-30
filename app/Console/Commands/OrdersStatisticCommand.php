<?php

namespace App\Console\Commands;

use App\Renders\OrdersStatistic\OrdersStatisticRender;
use App\Requests\Orders\OrdersRequest;
use App\Services\OrdersStatistic\OrdersStatisticService;
use Throwable;

readonly class OrdersStatisticCommand extends BaseCommand
{
    public function __construct(
        private OrdersStatisticService $service,
        private OrdersStatisticRender  $render,
    ) {}

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'orders_statistic';
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Aggregate and render orders statistic. Additional params: drivers, cities, date.';
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
            'date' => $filters['date'] ?? null,
        ]);

        $statistic = $this->service->handle($request);
        $this->render->render($statistic);
    }
}
