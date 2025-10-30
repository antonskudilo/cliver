<?php

namespace App\Pivots\Common;

abstract class PivotModel
{
    /**
     * @var array
     */
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    abstract public function getSourceName(): string;

    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }
}
