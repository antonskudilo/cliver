<?php

namespace App\Console\Commands;

use App\Renders\Cars\CarsRender;
use App\Requests\Cars\CarsRequest;
use App\Services\Cars\CarsService;
use Throwable;

readonly class CarsCommand extends BaseCommand
{
    public function __construct(
        private CarsService $service,
        private CarsRender  $render,
    ) {}

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'cars';
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Show list of cars. Additional params: model, number, driverIds, limit';
    }

    /**
     * @param array $args
     * @return void
     * @throws Throwable
     */
    public function execute(array $args): void
    {
        $filters = $this->parseFilters($args);

        $request = CarsRequest::fromArray([
            'name' => $filters['name'] ?? null,
            'number' => $filters['number'] ?? null,
            'driverIds' => $filters['driverIds'] ?? null,
            'limit' => $filters['limit'] ?? null,
        ]);

        $cars = $this->service->fetchWithRelations($request);
        $this->render->render($cars);
    }
}
