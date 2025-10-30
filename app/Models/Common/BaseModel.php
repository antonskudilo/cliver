<?php

namespace App\Models\Common;

abstract class BaseModel
{
    /**
     * @return int|string
     */
    abstract public function getId(): int|string;
}
