<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Nova\Filters\Filter;

class RecentlyCreatedFilter extends Filter
{

    const TODAY     = 'today';
    const YESTERDAY = 'yesterday';
    const WEEK      = 'week';
    const MONTH     = 'month';
    const YEAR      = 'year';

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
        return __('nova.recently_created');
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
        switch ($value) {
            case self::TODAY:
                return $query->whereDate('created_at', Carbon::now());
            case self::YESTERDAY:
                return $query->whereDate('created_at', '>=', Carbon::now()->yesterday());
            case self::WEEK:
                return $query->whereDate('created_at', '>=', Carbon::now()->startOfWeek());
            case self::MONTH:
                return $query->whereDate('created_at', '>=', Carbon::now()->startOfMonth());
            case self::YEAR:
                return $query->whereDate('created_at', '>=', Carbon::now()->startOfYear());
            default:
                return $query;
        }
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            __('nova.today') => self::TODAY,
            __('nova.yesterday') => self::YESTERDAY,
            __('nova.this_week') => self::WEEK,
            __('nova.this_month') => self::MONTH,
            __('nova.this_year') => self::YEAR
        ];
    }
}
