<?php

declare(strict_types=1);

namespace App\Nova\Filters\Base;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Laravel\Nova\Filters\DateFilter;

/**
 * Class DeletedStartDateFilter.
 */
class DeletedStartDateFilter extends DateFilter
{
    /**
     * Get the displayable name of the filter.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
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
        $value = Date::parse($value);

        return $query->where('deleted_at', ComparisonOperator::GTE, $value);
    }
}
