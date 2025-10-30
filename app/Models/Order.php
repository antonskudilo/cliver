<?php

namespace App\Models;

use App\Models\Common\BaseModel;
use DateTimeImmutable;

class Order extends BaseModel
{
    public function __construct(
        private readonly int                        $id,
        private readonly int                        $city_id,
        private readonly int                        $driver_id,
        private readonly int                        $sum,
        private readonly DateTimeImmutable          $date
    ) {}

    private ?Driver $driver = null;
    private ?City $city = null;

    /**
     * @param Driver|null $driver
     * @return void
     */
    public function setDriver(?Driver $driver): void
    {
        $this->driver = $driver;
    }

    /**
     * @param City|null $city
     * @return void
     */
    public function setCity(?City $city): void
    {
        $this->city = $city;
    }

    /**
     * @return Driver|null
     */
    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCityId(): int
    {
        return $this->city_id;
    }

    /**
     * @return int
     */
    public function getDriverId(): int
    {
        return $this->driver_id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getSum(): int
    {
        return $this->sum;
    }
}
