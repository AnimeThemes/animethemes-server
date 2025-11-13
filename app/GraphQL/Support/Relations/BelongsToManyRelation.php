<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Relations;

use App\Enums\GraphQL\PaginationType;
use App\GraphQL\Schema\Types\ConnectionType;
use App\GraphQL\Schema\Types\EdgeType;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\SortArgument;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class BelongsToManyRelation extends Relation
{
    protected EdgeType $edgeType;
    protected ConnectionType $connectionType;

    public function __construct(
        protected EloquentType $ownerType,
        protected string $nodeType,
        protected string $relationName,
        protected PivotType|string|null $pivotType = null,
    ) {
        $this->edgeType = new EdgeType($ownerType, $nodeType, $pivotType);
        $this->connectionType = new ConnectionType($this->edgeType);

        GraphQL::addType($this->connectionType, $this->connectionType->getName());

        parent::__construct(new $nodeType, $relationName);

        $this->pivotType = class_exists($pivotType ?? '') ? new $pivotType : null;
    }

    /**
     * Resolve the arguments of the sub-query.
     *
     * @return Argument[]
     */
    protected function arguments(): array
    {
        $pivotType = $this->pivotType;

        if ($pivotType instanceof PivotType) {
            return [
                ...parent::arguments(),

                new SortArgument($this->baseType, $pivotType),
            ];
        }

        return [];
    }

    /**
     * Get the edge type of the belongs to many relationship.
     */
    public function getEdgeType(): EdgeType
    {
        return $this->edgeType;
    }

    /**
     * Get the pivot type of the belongs to many relationship.
     */
    public function getPivotType(): ?PivotType
    {
        return $this->pivotType;
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type($this->connectionType->getName()));
    }

    /**
     * The pagination type if applicable.
     */
    public function paginationType(): PaginationType
    {
        return PaginationType::CONNECTION;
    }
}
