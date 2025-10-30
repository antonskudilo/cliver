<?php

namespace App\Services\Cars;

use App\Collections\Cars\CarsCollection;
use App\Repositories\CarsRepository;
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

            ->whereHas('drivers', ['name' => 'ivan'])

//            ->if(isset($request->driverIds), fn(CarsRepository $repo) => $repo->whereDriverId($request->driverIds))
//            ->if(isset($request->name), fn(CarsRepository $repo) => $repo->whereName($request->name))
//            ->if(isset($request->number), fn(CarsRepository $repo) => $repo->whereNumber($request->number))
            ->if(isset($request->limit), fn(CarsRepository $repo) => $repo->limit($request->limit))
            ->get();

        return CarsCollection::fromIterable($iterator);
    }
}
