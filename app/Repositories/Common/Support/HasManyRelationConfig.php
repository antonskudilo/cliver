<?php

namespace App\Repositories\Common\Support;

final class HasManyRelationConfig extends RelationConfig
{
    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return true;
    }
}
