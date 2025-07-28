<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

use App\GraphQL\Support\Relations\Relation;

interface HasRelations
{
    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array;
}
