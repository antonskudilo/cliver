<?php

namespace App\Repositories\Common\Support;

final class HasOneRelationConfig extends RelationConfig
{
    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return false;
    }
}
