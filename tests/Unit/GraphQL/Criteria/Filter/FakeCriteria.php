<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Criteria\Filter;

use App\GraphQL\Criteria\Filter\FilterCriteria;
use Illuminate\Database\Eloquent\Builder;

class FakeCriteria extends FilterCriteria
{
    /**
     * Apply the filtering to the current Eloquent builder.
     */
    public function filter(Builder $builder): Builder
    {
        return $builder;
    }
}
