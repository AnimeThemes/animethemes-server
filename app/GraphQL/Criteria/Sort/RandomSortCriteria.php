<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use App\GraphQL\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;

class RandomSortCriteria extends SortCriteria
{
    public function __construct()
    {
        // Random Sort doesn't need a field so we fake it.
        parent::__construct(new Sort(''));
    }

    /**
     * Build the enum case.
     */
    public function __toString(): string
    {
        return 'RANDOM';
    }

    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder): Builder
    {
        return $builder->inRandomOrder();
    }
}
