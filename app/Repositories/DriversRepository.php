<?php

namespace App\Repositories;

use App\Collections\Orders\OrdersCollection;
use App\Factories\Models\Drivers\DriverFactoryInterface;
use App\Models\Driver;
use App\Providers\AppResolver;
use App\Repositories\Common\BaseRepository;
use App\Repositories\Common\Support\HasManyRelation;
use App\Repositories\Common\Support\Relation;
use App\Repositories\Common\SupportsRelations;
use Throwable;

/**
 * @extends BaseRepository<Driver,DriversRepository>
 */
class DriversRepository extends BaseRepository implements SupportsRelations
{
    public function __construct(
        private readonly DriverFactoryInterface $factory,
        protected AppResolver                   $resolver
    )
    {
        parent::__construct($resolver);
    }

    /**
     * @return string
     */
    protected function sourceName(): string
    {
        return 'drivers';
    }

    /**
     * @param array $row
     * @return Driver
     * @throws Throwable
     */
    protected function mapRow(array $row): Driver
    {
        return $this->factory->fromArray($row);
    }

    /**
     * @return Relation[]
     */
    public function getRelationMap(): array
    {
        return [
            'orders' => HasManyRelation::make(
                name: 'orders',
                relatedRepositoryClass: OrdersRepository::class,
                localKey: 'id',
                foreignKey: 'id',
                foreignKeySelector: fn(Driver $driver) => $driver->getId(),
                relatedKey: 'driver_id',
                setter: fn(Driver $driver, ?array $orders) => $driver->setOrders(OrdersCollection::fromIterable($orders ?? [])),
                accessor: fn(Driver $driver) => $driver->getOrders(),
            ),
        ];
    }

    /**
     * @param array|string|int $id
     * @return static
     */
    public function whereId(array|string|int $id): static
    {
        $id = array_map('intval', (array) $id);

        return $this->addCondition('id', $id);
    }

    /**
     * @param array|string|int $cityId
     * @return static
     * @throws Throwable
     */
    public function whereHasOrderCityId(array|string|int $cityId): static
    {
        $cityId = array_map('intval', (array)$cityId);

        return $this->whereHas('orders', ['city_id' => $cityId]);
    }

    /**
     * @param array|string $date
     * @return static
     * @throws Throwable
     */
    public function whereHasOrderDate(array|string $date): static
    {
        return $this->whereHas('orders', ['date' => (array)$date]);
    }

    /**
     * @param string $phone
     * @return static
     */
    public function wherePhoneContains(string $phone): static
    {
        return $this->whereContains('phone', $phone);
    }

    /**
     * @param string $name
     * @return static
     */
    public function whereNameContains(string $name): static
    {
        return $this->whereContains('name', $name);
    }
}
