<?php

namespace App\Repositories\Common\Support;

final class HasManyRelation extends Relation
{
    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return true;
    }
}
