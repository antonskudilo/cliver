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
            ->if(isset($request->orderCityIds), fn(DriversRepository $repo) => $repo->whereHasOrderCityId($request->orderCityIds))
            ->if(isset($request->orderDate), fn(DriversRepository $repo) => $repo->whereHasOrderDate($request->orderDate))
            ->if(isset($request->phone), fn(DriversRepository $repo) => $repo->wherePhoneContains($request->phone))
            ->if(isset($request->name), fn(DriversRepository $repo) => $repo->whereNameContains($request->name))
            ->if(isset($request->driverIds), fn(DriversRepository $repo) => $repo->whereId($request->driverIds))
            ->if(isset($request->limit), fn(DriversRepository $repo) => $repo->limit($request->limit))
            ->get();

        return DriversCollection::fromIterable($iterator);
    }
}
