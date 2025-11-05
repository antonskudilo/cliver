<?php

namespace App\Repositories\Common\Support;

final class HasOneRelation extends Relation
{
    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return false;
    }
}
