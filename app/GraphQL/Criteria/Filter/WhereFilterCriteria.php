<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Filter;

use App\GraphQL\Argument\FilterArgument;
use App\GraphQL\Filter\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class WhereFilterCriteria extends FilterCriteria
{
    public function __construct(
        protected Filter $filter,
        protected $value,
        protected FilterArgument $argument,
    ) {
        parent::__construct($filter, $value);
    }

    /**
     * Apply the filtering to the current Eloquent builder.
     */
    public function filter(Builder $builder): Builder
    {
        return $builder->where(
            $builder->qualifyColumn($this->filter->getColumn()),
            $this->argument->getComparisonOperator()->value,
            Arr::first($this->filter->getFilterValues($this->getFilterValues())),
        );
    }
}
