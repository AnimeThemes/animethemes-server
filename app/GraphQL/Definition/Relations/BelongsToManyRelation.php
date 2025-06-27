<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Relations;

use App\Enums\GraphQL\RelationType;
use GraphQL\Type\Definition\Type;

/**
 * Class BelongsToManyRelation.
 */
class BelongsToManyRelation extends Relation
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    protected function type(): Type
    {
        if (! $this->nullable) {
            return Type::nonNull(Type::listOf($this->type));
        }

        return Type::listOf($this->type);
    }

    /**
     * The Relation type.
     *
     * @return RelationType
     */
    protected function relation(): RelationType
    {
        return RelationType::BELONGS_TO_MANY;
    }
}
