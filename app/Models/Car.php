<?php

namespace App\Models;

use App\Collections\Drivers\DriversRelationCollection;
use App\Models\Common\BaseModel;

class Car extends BaseModel
{
    /**
     * @var DriversRelationCollection|null
     */
    private ?DriversRelationCollection $drivers = null;

    public function __construct(
        private readonly int    $id,
        private readonly string $model,
        private readonly string $number
    ) {}

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return DriversRelationCollection|null
     */
    public function getDrivers(): ?DriversRelationCollection
    {
        return $this->drivers;
    }

    /**
     * @param DriversRelationCollection $drivers
     * @return void
     */
    public function setDrivers(DriversRelationCollection $drivers): void
    {
        $this->drivers = $drivers;
    }
}