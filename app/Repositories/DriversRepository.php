<?php

namespace App\Repositories;

use App\Collections\Orders\OrdersCollection;
use App\Factories\Models\Drivers\DriverFactoryInterface;
use App\Models\Driver;
use App\Providers\AppResolver;
use App\Repositories\Common\BaseRepository;
use App\Repositories\Common\Support\HasManyRelationConfig;
use App\Repositories\Common\Support\RelationConfig;
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
     * @return RelationConfig[]
     */
    public function getRelationMap(): array
    {
        return [
            'orders' => HasManyRelationConfig::make(
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
}
