<?php

namespace App\Repositories;

use App\Collections\Drivers\DriversRelationCollection;
use App\Factories\Models\Cars\CarFactoryInterface;
use App\Models\Car;
use App\Pivots\DriversCarsPivot;
use App\Providers\AppResolver;
use App\Repositories\Common\BaseRepository;
use App\Repositories\Common\Support\ManyToManyRelation;
use App\Repositories\Common\Support\Relation;
use App\Repositories\Common\SupportsRelations;
use Throwable;

/**
 * @extends BaseRepository<Car,CarsRepository>
 */
class CarsRepository extends BaseRepository implements SupportsRelations
{
    public function __construct(
        private readonly CarFactoryInterface $factory,
        protected AppResolver                $resolver
    )
    {
        parent::__construct($resolver);
    }

    /**
     * @return string
     */
    protected function sourceName(): string
    {
        return 'cars';
    }

    /**
     * @param array $row
     * @return Car
     * @throws Throwable
     */
    protected function mapRow(array $row): Car
    {
        return $this->factory->fromArray($row);
    }

    /**
     * @return Relation[]
     */
    public function getRelationMap(): array
    {
        return [
            'drivers' => ManyToManyRelation::makePivot(
                name: 'drivers',
                relatedRepositoryClass: DriversRepository::class,
                localKey: 'car_id',
                foreignKeySelector: fn(Car $car) => $car->getId(),
                foreignKey: 'car_id',
                relatedKey: 'driver_id',
                relatedLocalKey: 'id',
                setter: fn(Car $car, ?array $drivers, ?array $pivots) => $car->setDrivers(DriversRelationCollection::fromIterable($drivers, $pivots)),
                accessor: fn(Car $car) => $car->getDrivers(),
                pivot: new DriversCarsPivot(),
            ),
        ];
    }

    /**
     * @param array|string|int $driverId
     * @return static
     */
    public function whereDriverId(array|string|int $driverId): static
    {
        $driverId = array_map('intval', (array)$driverId);

        return $this->whereIn('driver_id', $driverId);
    }

    /**
     * @param string $model
     * @return static
     */
    public function whereModelContains(string $model): static
    {
        return $this->whereContains('model', $model);
    }

    /**
     * @param string $number
     * @return static
     */
    public function whereNumberContains(string $number): static
    {
        return $this->whereContains('number', $number);
    }

    /**
     * @param string $name
     * @return static
     * @throws Throwable
     */
    public function whereHasDriver(string $name): static
    {
        return $this->whereHas('drivers', fn(DriversRepository $repo) => $repo->whereNameContains($name));
    }
}
