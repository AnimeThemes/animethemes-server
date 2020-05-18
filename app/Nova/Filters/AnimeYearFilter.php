<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Filters\Filter;

class AnimeYearFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Get the displayable name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return __('nova.year');
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
        return $query->where('year', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        $options = [];

        for ($year = 1960; $year <= date('Y') + 1; $year++) {
            $options = Arr::add($options, $year, $year);
        }

        return $options;
    }
}
