<?php

namespace App\Repositories;

use App\Collections\Drivers\DriversRelationCollection;
use App\Factories\Models\Cars\CarFactoryInterface;
use App\Models\Car;
use App\Pivots\DriversCarsPivot;
use App\Providers\AppResolver;
use App\Repositories\Common\BaseRepository;
use App\Repositories\Common\Support\ManyToManyRelationConfig;
use App\Repositories\Common\Support\RelationConfig;
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
     * @return RelationConfig[]
     */
    public function getRelationMap(): array
    {
        return [
            'drivers' => ManyToManyRelationConfig::makePivot(
                name: 'drivers',
                relatedRepositoryClass: DriversRepository::class,
                localKey: 'car_id',
                foreignKeySelector: fn(Car $car) => $car->getId(),
                foreignKey: 'car_id',
                relatedKey: 'driver_id',
                relatedLocalKey: 'id',
                setter: fn(Car $car, ?array $drivers, ?array $pivots) => $car->setDrivers(DriversRelationCollection::fromIterable($drivers, $pivots)),
                accessor: fn(Car $car) => $car->getDrivers(),
                pivot: new DriversCarsPivot() // или просто 'drivers_cars',
            ),
        ];
    }
}
