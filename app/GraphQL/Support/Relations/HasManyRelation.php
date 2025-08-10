<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Relations;

use App\Enums\GraphQL\PaginationType;
use App\Enums\GraphQL\RelationType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class HasManyRelation extends Relation
{
    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::nonNull(GraphQL::paginate($this->rebingType->getName()));
    }

    /**
     * The Relation type.
     */
    protected function relation(): RelationType
    {
        return RelationType::HAS_MANY;
    }

    /**
     * The pagination type if applicable.
     */
    public function paginationType(): PaginationType
    {
        return PaginationType::PAGINATOR;
    }
}
