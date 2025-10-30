<?php

namespace App\Repositories\Common;

use App\Repositories\Common\Support\RelationConfig;

interface SupportsRelations
{
    /**
     * @return RelationConfig[]
     */
    public function getRelationMap(): array;
}
