<?php

declare(strict_types=1);

namespace App\Nova\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Nova\Filters\DateFilter;

/**
 * Class DeletedStartDateFilter.
 */
class DeletedStartDateFilter extends DateFilter
{
    /**
     * Get the displayable name of the filter.
     *
     * @return array|string|null
     */
    public function name(): array | string | null
    {
        return __('nova.deleted_at_start');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param Request $request
     * @param Builder $query
     * @param mixed $value
     * @return Builder
     */
    public function apply(Request $request, $query, $value): Builder
    {
        $value = Carbon::parse($value);

        return $query->where('deleted_at', ComparisonOperator::GTE, $value);
    }
}
