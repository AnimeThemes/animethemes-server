<?php

declare(strict_types=1);

namespace App\GraphQL\Sort;

use App\Enums\GraphQL\QualifyColumn;
use Illuminate\Database\Eloquent\Builder;

class FieldSortCriteria extends SortCriteria
{
    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder): Builder
    {
        $column = $this->qualifyColumn === QualifyColumn::YES
            ? $builder->qualifyColumn($this->column)
            : $this->column;

        return $builder->orderBy($column, $this->direction->value);
    }
}
