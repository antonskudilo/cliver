<?php

namespace App\Services\Cars;

use App\Collections\Cars\CarsCollection;
use App\Repositories\CarsRepository;
use App\Repositories\DriversRepository;
use App\Requests\Cars\CarsRequest;
use Throwable;

final readonly class CarsService
{
    public function __construct(
        private CarsRepository $repository,
    ) {}

    /**
     * @param CarsRequest $request
     * @return CarsCollection
     * @throws Throwable
     */
    public function fetchWithRelations(CarsRequest $request): CarsCollection
    {
        $iterator = $this->repository
            ->withRelation(['drivers'])
            ->if(isset($request->driver), fn(CarsRepository $repo) => $repo->whereHasDriver($request->driver))
            ->if(isset($request->driverIds), fn(CarsRepository $repo) => $repo->whereDriverId($request->driverIds))
            ->if(isset($request->model), fn(CarsRepository $repo) => $repo->whereModelContains($request->model))
            ->if(isset($request->number), fn(CarsRepository $repo) => $repo->whereNumberContains($request->number))
            ->if(isset($request->limit), fn(CarsRepository $repo) => $repo->limit($request->limit))
            ->get();

        return CarsCollection::fromIterable($iterator);
    }
}
