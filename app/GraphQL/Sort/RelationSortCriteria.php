<?php

declare(strict_types=1);

namespace App\GraphQL\Sort;

use App\Enums\GraphQL\SortDirection;
use Illuminate\Database\Eloquent\Builder;

class RelationSortCriteria extends SortCriteria
{
    public function __construct(
        protected string $enumName,
        protected string $column,
        protected string $relation,
        protected SortDirection $direction = SortDirection::ASC,
        protected bool $isStringField = false,
    ) {}

    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder): Builder
    {
        $relation = $builder->getRelation($this->relation);

        $orderBySubQuery = $relation->getRelationExistenceQuery($relation->getQuery(), $builder, [$this->column]);

        return $builder->orderBy($orderBySubQuery->toBase(), $this->direction->value);
    }
}
