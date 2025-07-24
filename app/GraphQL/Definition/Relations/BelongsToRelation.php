<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Relations;

use App\Enums\GraphQL\RelationType;
use GraphQL\Type\Definition\Type;

class BelongsToRelation extends Relation
{
    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        if (! $this->nullable) {
            return Type::nonNull($this->type);
        }

        return $this->type;
    }

    /**
     * The Relation type.
     */
    protected function relation(): RelationType
    {
        return RelationType::BELONGS_TO;
    }
}
