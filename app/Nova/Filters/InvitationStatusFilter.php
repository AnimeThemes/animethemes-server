<?php

namespace App\Nova\Filters;

use App\Enums\InvitationStatus;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class InvitationStatusFilter extends Filter
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
     * @return array|string|null
     */
    public function name()
    {
        return __('nova.status');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('status', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        return array_flip(InvitationStatus::asSelectArray());
    }
}
