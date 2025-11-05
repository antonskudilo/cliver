<?php

namespace App\Console\Commands;

use App\Renders\Drivers\DriversRender;
use App\Requests\Drivers\DriversRequest;
use App\Services\Drivers\DriversService;
use Throwable;

readonly class DriversCommand extends BaseCommand
{
    public function __construct(
        private DriversService $service,
        private DriversRender  $render,
    ) {}

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'drivers';
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Show list of drivers. Additional params: drivers, order_city, order_date, phone, name, limit';
    }

    /**
     * @param array $args
     * @return void
     * @throws Throwable
     */
    public function execute(array $args): void
    {
        $filters = $this->parseFilters($args);

        $request = DriversRequest::fromArray([
            'drivers' => $filters['drivers'] ?? null,
            'order_city' => $filters['order_city'] ?? null,
            'order_date' => $filters['order_date'] ?? null,
            'phone' => $filters['phone'] ?? null,
            'name' => $filters['name'] ?? null,
            'limit' => $filters['limit'] ?? null,
        ]);

        $drivers = $this->service->fetchWithRelations($request);
        $this->render->render($drivers);
    }
}
