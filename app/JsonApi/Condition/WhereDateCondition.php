<?php

namespace App\JsonApi\Condition;

use App\JsonApi\Filter\Filter;
use Illuminate\Database\Eloquent\Builder;

class WhereDateCondition extends WhereCondition
{
    /**
     * Apply condition to builder through filter.
     *
     * @param Builder $builder
     * @param Filter $filter
     * @return Builder $builder
     */
    public function apply(Builder $builder, Filter $filter)
    {
        return $builder->whereDate(
            $this->getField(),
            optional($this->getComparisonOperator())->value,
            collect($filter->getFilterValues($this))->first(),
            $this->getLogicalOperator()->value
        );
    }
}
