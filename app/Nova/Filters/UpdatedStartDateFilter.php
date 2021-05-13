<?php

namespace App\Nova\Filters;

use App\Enums\Filter\ComparisonOperator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Nova\Filters\DateFilter;

class UpdatedStartDateFilter extends DateFilter
{
    /**
     * Get the displayable name of the filter.
     *
     * @return array|string|null
     */
    public function name()
    {
        return __('nova.updated_at_start');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        $value = Carbon::parse($value);

        return $query->where(Model::UPDATED_AT, ComparisonOperator::GTE, $value);
    }
}
