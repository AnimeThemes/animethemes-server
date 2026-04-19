<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PivotSortCriteria extends SortCriteria
{
    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder, ?BelongsToMany $relation = null): Builder
    {
        $column = $this->sortCase->shouldQualifyColumn()
            ? $relation?->qualifyPivotColumn($this->column)
            : $this->column;

        return $builder->orderBy($column, $this->getDirection()->value);
    }
}
