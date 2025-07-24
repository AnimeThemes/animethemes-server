<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Relations;

use App\Enums\GraphQL\RelationType;
use App\GraphQL\Definition\Types\Edges\BaseEdgeType;
use GraphQL\Type\Definition\Type;

class BelongsToManyRelation extends Relation
{
    /**
     * @param  BaseEdgeType  $edge
     * @param  string  $relationName
     * @param  string  $field
     */
    public function __construct(
        protected BaseEdgeType $edge,
        protected string $relationName,
        protected ?string $field = null,
    ) {
        $test = $edge::getNodeType();
        parent::__construct(new $test, $relationName, $field, $edge::class, true);
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
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
