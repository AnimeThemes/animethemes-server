<?php

declare(strict_types=1);

namespace App\Nova\Filters\Base;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Laravel\Nova\Filters\DateFilter;

/**
 * Class CreatedEndDateFilter.
 */
class CreatedEndDateFilter extends DateFilter
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
        return __('nova.created_at_end');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  Request  $request
     * @param  Builder  $query
     * @param  mixed  $value
     * @return Builder
     */
    public function apply(Request $request, $query, $value): Builder
    {
        $value = Date::parse($value);

        return $query->where(BaseModel::ATTRIBUTE_CREATED_AT, ComparisonOperator::LTE, $value);
    }
}
