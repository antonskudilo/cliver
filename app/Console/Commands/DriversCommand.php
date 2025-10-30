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
        return 'Show list of drivers. Additional params: drivers, limit';
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
            'limit' => $filters['limit'] ?? null,
        ]);

        $drivers = $this->service->fetchWithRelations($request);
        $this->render->render($drivers);
    }
}
