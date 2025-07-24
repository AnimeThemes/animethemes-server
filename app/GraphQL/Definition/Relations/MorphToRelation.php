<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Relations;

use App\Enums\GraphQL\RelationType;
use GraphQL\Type\Definition\Type;

class MorphToRelation extends Relation
{
    /**
     * The type returned by the field.
     *
     * @return Type
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
     *
     * @return RelationType
     */
    protected function relation(): RelationType
    {
        return RelationType::MORPH_TO;
    }
}
