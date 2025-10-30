<?php

namespace App\Services\Drivers;

use App\Collections\Drivers\DriversCollection;
use App\Repositories\DriversRepository;
use App\Requests\Drivers\DriversRequest;
use Throwable;

final readonly class DriversService
{
    public function __construct(
        private DriversRepository $repository,
    ) {}

    /**
     * @param DriversRequest $request
     * @return DriversCollection
     * @throws Throwable
     */
    public function fetchWithRelations(DriversRequest $request): DriversCollection
    {
        $iterator = $this->repository
            ->withRelation(['orders.city'])

            ->whereHas('orders', ['city_id' => '96']) // TODO: test

            ->if(isset($request->driverIds), fn(DriversRepository $repo) => $repo->whereId($request->driverIds))
            ->if(isset($request->limit), fn(DriversRepository $repo) => $repo->limit($request->limit))
            ->get();

        return DriversCollection::fromIterable($iterator);
    }
}
