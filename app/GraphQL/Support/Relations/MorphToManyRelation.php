<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Relations;

use App\Enums\GraphQL\PaginationType;
use App\GraphQL\Schema\Types\ConnectionType;
use App\GraphQL\Schema\Types\EdgeType;
use App\GraphQL\Schema\Types\EloquentType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class MorphToManyRelation extends Relation
{
    protected EdgeType $edgeType;
    protected ConnectionType $connectionType;

    public function __construct(
        protected EloquentType $ownerType,
        protected string $nodeType,
        protected string $relationName,
        protected ?string $pivotType = null,
    ) {
        $this->edgeType = new EdgeType($ownerType, $nodeType, $pivotType);
        $this->connectionType = new ConnectionType($this->edgeType);

        GraphQL::addType($this->connectionType, $this->connectionType->getName());

        parent::__construct(new $nodeType, $relationName);
    }

    /**
     * Get the edge type of the belongs to many relationship.
     */
    public function getEdgeType(): EdgeType
    {
        return $this->edgeType;
    }

    public function type(): Type
    {
        return GraphQL::type($this->connectionType->getName());
    }

    /**
     * The pagination type if applicable.
     */
    public function paginationType(): PaginationType
    {
        return PaginationType::CONNECTION;
    }
}
