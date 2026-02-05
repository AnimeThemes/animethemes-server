<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use App\Enums\GraphQL\Sort\SortDirection;
use App\GraphQL\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class RelationSortCriteria extends SortCriteria
{
    public function __construct(
        protected Sort $sort,
        protected string $relation,
        protected SortDirection $direction = SortDirection::ASC,
        protected bool $isStringField = false,
    ) {
        parent::__construct($sort, $direction, $isStringField);
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * Build the enum case for a direction.
     * Template: {RELATION}_{FIELD_NAME}.
     * Template: {RELATION}_{FIELD_NAME}_DESC.
     */
    public function __toString(): string
    {
        $name = Str::of($this->getRelation())
            ->append('_')
            ->append($this->getSort()->getName())
            ->snake()
            ->upper();

        return (string) match ($this->direction) {
            SortDirection::ASC => $name,
            SortDirection::DESC => $name->append('_DESC'),
        };
    }

    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder): Builder
    {
        $column = $this->getSort()->getColumn();

        return $builder->orderBy("{$this->getRelation()}_$column", $this->direction->value);
    }
}
