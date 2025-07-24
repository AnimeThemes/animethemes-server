<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Scope\Scope;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;

class FieldCriteria extends Criteria
{
    /**
     * Create a new criteria instance.
     */
    public function __construct(Scope $scope, string $field, protected readonly Direction $direction)
    {
        parent::__construct($scope, $field);
    }

    /**
     * Get the sort direction.
     */
    public function getDirection(): Direction
    {
        return $this->direction;
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    public function sort(Builder $builder, Sort $sort): Builder
    {
        $column = $sort->shouldQualifyColumn()
            ? $builder->qualifyColumn($sort->getColumn())
            : $sort->getColumn();

        return $builder->orderBy($column, $this->direction->value);
    }
}
