<?php

namespace App\Repositories;

use App\Collections\Orders\OrdersCollection;
use App\Factories\Models\Cities\CityFactoryInterface;
use App\Models\City;
use App\Providers\AppResolver;
use App\Repositories\Common\BaseRepository;
use App\Repositories\Common\Support\HasManyRelationConfig;
use App\Repositories\Common\Support\RelationConfig;
use App\Repositories\Common\SupportsRelations;
use Throwable;

/**
 * @extends BaseRepository<City,CitiesRepository>
 */
class CitiesRepository extends BaseRepository implements SupportsRelations
{
    public function __construct(
        private readonly CityFactoryInterface $factory,
        protected AppResolver                 $resolver
    )
    {
        parent::__construct($resolver);
    }

    /**
     * @return string
     */
    protected function sourceName(): string
    {
        return 'cities';
    }

    /**
     * @param array $row
     * @return City
     * @throws Throwable
     */
    protected function mapRow(array $row): City
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
                localKey: 'city_id',
                foreignKey: 'id',
                foreignKeySelector: fn(City $city) => $city->getId(),
                relatedKey: 'city_id',
                setter: fn(City $city, ?array $orders) => $city->setOrders(OrdersCollection::fromIterable($orders ?? [])),
                accessor: fn(City $city) => $city->getOrders(),
            ),
        ];
    }

    /**
     * @param string $name
     * @return static
     */
    public function whereName(string $name): static
    {
        return $this->addCondition('name', $name);
    }
}
