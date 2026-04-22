<?php

declare(strict_types=1);

namespace App\GraphQL\Sort;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PivotSortCriteria extends SortCriteria
{
    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder, ?BelongsToMany $relation = null): Builder
    {
        // TODO: Double check how to pass a $relation.
        // $column = $this->qualifyColumn === QualifyColumn::YES
        //     ? $relation?->qualifyPivotColumn($this->column)
        //     : $this->column;

        $column = $this->column;

        return $builder->orderBy($column, $this->getDirection()->value);
    }
}
