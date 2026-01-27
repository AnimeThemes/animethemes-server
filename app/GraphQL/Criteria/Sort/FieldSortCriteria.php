<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use Illuminate\Database\Eloquent\Builder;

class FieldSortCriteria extends SortCriteria
{
    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder): Builder
    {
        $sort = $this->field->getSort();

        $column = $sort->shouldQualifyColumn()
            ? $builder->qualifyColumn($sort->getColumn())
            : $sort->getColumn();

        return $builder->orderBy($column, $this->direction->value);
    }
}
