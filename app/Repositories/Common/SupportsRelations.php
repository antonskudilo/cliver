<?php

namespace App\Repositories\Common;

use App\Repositories\Common\Support\Relation;

interface SupportsRelations
{
    /**
     * @return Relation[]
     */
    public function getRelationMap(): array;
}
