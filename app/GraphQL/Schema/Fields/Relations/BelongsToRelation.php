<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Relations;

use App\Enums\GraphQL\PaginationType;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;

class BelongsToRelation extends Relation
{
    /**
     * Resolve the relation.
     *
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return Arr::get($root, $this->getRelationName());
    }

    /**
     * The pagination type if applicable.
     */
    public function paginationType(): PaginationType
    {
        return PaginationType::NONE;
    }
}
