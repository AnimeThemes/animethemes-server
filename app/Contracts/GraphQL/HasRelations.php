<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

use App\GraphQL\Definition\Relations\Relation;

/**
 * Interface HasRelations.
 */
interface HasRelations
{
    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array;
}
