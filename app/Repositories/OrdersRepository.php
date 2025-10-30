<?php

namespace App\Repositories;

use App\Factories\Models\Orders\OrderFactoryInterface;
use App\Models\City;
use App\Models\Driver;
use App\Models\Order;
use App\Providers\AppResolver;
use App\Repositories\Common\BaseRepository;
use App\Repositories\Common\Support\HasOneRelationConfig;
use App\Repositories\Common\Support\RelationConfig;
use App\Repositories\Common\SupportsRelations;
use DateTimeInterface;
use Throwable;

/**
 * @extends BaseRepository<Order,OrdersRepository>
 */
class OrdersRepository extends BaseRepository implements SupportsRelations
{
    public function __construct(
        private readonly OrderFactoryInterface $factory,
        protected AppResolver                  $resolver
    )
    {
        parent::__construct($resolver);
    }

    /**
     * @return string
     */
    protected function sourceName(): string
    {
        return 'orders';
    }

    /**
     * @param array $row
     * @return Order
     * @throws Throwable
     */
    protected function mapRow(array $row): Order
    {
        return $this->factory->fromArray($row);
    }

    /**
     * @return RelationConfig[]
     */
    public function getRelationMap(): array
    {
        return [
            'driver' => HasOneRelationConfig::make(
                name: 'driver',
                relatedRepositoryClass: DriversRepository::class,
                localKey: 'driver_id',
                foreignKey: 'id',
                foreignKeySelector: fn(Order $order) => $order->getDriverId(),
                relatedKey: 'id',
                setter: fn(Order $order, ?Driver $driver) => $order->setDriver($driver),
                accessor: fn(Order $order) => $order->getDriver(),
            ),
            'city' => HasOneRelationConfig::make(
                name: 'city',
                relatedRepositoryClass: CitiesRepository::class,
                localKey: 'city_id',
                foreignKey: 'id',
                foreignKeySelector: fn(Order $order) => $order->getCityId(),
                relatedKey: 'id',
                setter: fn(Order $order, ?City $city) => $order->setCity($city),
                accessor: fn(Order $order) => $order->getCity(),
            ),
        ];
    }

    /**
     * @param array|string|int $driverId
     * @return static
     */
    public function whereDriverId(array|string|int $driverId): static
    {
        $driverId = array_map('intval', (array) $driverId);

        return $this->addCondition('driver_id', $driverId);
    }

    /**
     * @param array|string|int $cityId
     * @return static
     */
    public function whereCityId(array|string|int $cityId): static
    {
        $cityId = array_map('intval', (array) $cityId);

        return $this->addCondition('city_id', $cityId);
    }

    /**
     * @param DateTimeInterface $date
     * @return static
     */
    public function whereDate(DateTimeInterface $date): static
    {
        return $this->addCondition('date', $date->format('Y-m-d'));
    }
}
