<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class FieldSort.
 */
class FieldCriteria extends Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  string  $field
     * @param  Direction  $direction
     */
    public function __construct(string $field, protected Direction $direction)
    {
        parent::__construct($field);
    }

    /**
     * Get the sort direction.
     *
     * @return Direction
     */
    public function getDirection(): Direction
    {
        return $this->direction;
    }

    /**
     * Apply criteria to builder.
     *
     * @param Builder $builder
     * @param Sort $sort
     * @return Builder
     */
    public function sort(Builder $builder, Sort $sort): Builder
    {
        return $builder->orderBy($sort->getColumn(), $this->direction->value);
    }
}
