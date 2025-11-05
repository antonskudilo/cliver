<?php

namespace App\Services\Cities;

use App\Collections\Cities\CitiesCollection;
use App\Repositories\CitiesRepository;
use App\Requests\Cities\CitiesRequest;
use Throwable;

final readonly class CitiesService
{
    public function __construct(
        private CitiesRepository $repository,
    ) {}

    /**
     * @param CitiesRequest $request
     * @return CitiesCollection
     * @throws Throwable
     */
    public function fetchWithRelations(CitiesRequest $request): CitiesCollection
    {
        $iterator = $this->repository
            ->withRelation('orders')
            ->if(isset($request->name), fn(CitiesRepository $repo) => $repo->whereNameContains($request->name))
            ->if(isset($request->limit), fn(CitiesRepository $repo) => $repo->limit($request->limit))
            ->get();

        return CitiesCollection::fromIterable($iterator);
    }
}
