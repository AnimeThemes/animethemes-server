<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use Exception;
use Illuminate\Database\Eloquent\Builder;

class CountSortCriteria extends SortCriteria
{
    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder): Builder
    {
        try {
            /** @phpstan-ignore-next-line */
            $relation = $this->field->{'relation'}();
        } catch (Exception) {
            throw new Exception("'relation' method is required for the aggregate sort type");
        }

        return $builder->withCount($relation)->orderBy("{$relation}_count", $this->direction->value);
    }
}
