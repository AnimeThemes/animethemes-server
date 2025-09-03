<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Relations;

use App\Enums\GraphQL\PaginationType;
use App\GraphQL\Schema\Types\EdgeConnectionType;
use App\GraphQL\Schema\Types\EdgeType;
use App\GraphQL\Schema\Types\EloquentType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class BelongsToManyRelation extends Relation
{
    protected EdgeType $edgeType;
    protected EdgeConnectionType $edgeConnectionType;

    public function __construct(
        protected EloquentType $ownerType,
        protected string $nodeType,
        protected string $relationName,
        protected ?string $pivotType = null,
    ) {
        $this->edgeType = new EdgeType($ownerType, $nodeType, $pivotType);
        $this->edgeConnectionType = new EdgeConnectionType($this->edgeType);

        GraphQL::addType($this->edgeConnectionType, $this->edgeConnectionType->getName());

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
        return GraphQL::type($this->edgeConnectionType->getName());
    }

    /**
     * The pagination type if applicable.
     */
    public function paginationType(): PaginationType
    {
        return PaginationType::CONNECTION;
    }
}
