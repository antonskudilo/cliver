<?php

namespace App\Console\Commands;

use App\Renders\Cities\CitiesRender;
use App\Requests\Cities\CitiesRequest;
use App\Services\Cities\CitiesService;
use Throwable;

readonly class CitiesCommand extends BaseCommand
{
    public function __construct(
        private CitiesService $service,
        private CitiesRender  $render,
    )
    {
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'cities';
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Show list of cities. Additional params: name, limit';
    }

    /**
     * @param array $args
     * @return void
     * @throws Throwable
     */
    public function execute(array $args): void
    {
        $filters = $this->parseFilters($args);

        $request = CitiesRequest::fromArray([
            'name' => $filters['name'] ?? null,
            'limit' => $filters['limit'] ?? null,
        ]);

        $cities = $this->service->fetchWithRelations($request);
        $this->render->render($cities);
    }
}
