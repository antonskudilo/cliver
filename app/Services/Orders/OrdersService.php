<?php

namespace App\Services\Orders;

use App\Collections\Orders\OrdersCollection;
use App\Repositories\CitiesRepository;
use App\Repositories\OrdersRepository;
use App\Requests\Orders\OrdersRequest;
use Throwable;

final readonly class OrdersService
{
    public function __construct(
        private OrdersRepository $repository,
    ) {}

    /**
     * @param OrdersRequest $request
     * @return OrdersCollection
     * @throws Throwable
     */
    public function fetchWithRelations(OrdersRequest $request): OrdersCollection
    {
        $iterator = $this->repository
            ->withRelation(['driver', 'city'])
            ->if(isset($request->city), fn(OrdersRepository $repo) => $repo->whereHasCity($request->city))
            ->if(isset($request->driver), fn(OrdersRepository $repo) => $repo->whereHasDriver($request->driver))
            ->if(isset($request->driverIds), fn(OrdersRepository $repo) => $repo->whereDriverId($request->driverIds))
            ->if(isset($request->cityIds), fn(OrdersRepository $repo) => $repo->whereCityId($request->cityIds))
            ->if(isset($request->date), fn(OrdersRepository $repo) => $repo->whereDate($request->date))
            ->if(isset($request->limit), fn(OrdersRepository $repo) => $repo->limit($request->limit))
            ->if(isset($request->orderBy), fn(OrdersRepository $repo) => $repo->orderBy($request->orderBy, $request->direction))
            ->get();

        return OrdersCollection::fromIterable($iterator);
    }
}
