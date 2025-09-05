<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Relations;

use App\Enums\GraphQL\PaginationType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class MorphManyRelation extends Relation
{
    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type($this->baseType->getName()))));
    }

    /**
     * The pagination type if applicable.
     */
    public function paginationType(): PaginationType
    {
        return PaginationType::SIMPLE;
    }
}
