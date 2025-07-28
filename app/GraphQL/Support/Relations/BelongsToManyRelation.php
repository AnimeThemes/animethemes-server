<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Relations;

use App\Enums\GraphQL\RelationType;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Support\EdgeType;
use GraphQL\Type\Definition\Type;

class BelongsToManyRelation extends Relation
{
    public function __construct(
        protected EloquentType $parentType,
        protected string $nodeType,
        protected string $relationName,
        protected ?string $pivotType = null,
    ) {
        $this->edgeType = new EdgeType($parentType, $nodeType, $pivotType);

        $nodeType = $this->edgeType->getNodeType();

        parent::__construct(new $nodeType, $relationName);
    }

    /**
     * Get the edge type of the belongs to many relationship.
     */
    public function getEdgeType(): EdgeType
    {
        return $this->edgeType;
    }

    /**
     * The type returned by the field.
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
     */
    protected function relation(): RelationType
    {
        return RelationType::BELONGS_TO_MANY;
    }
}
