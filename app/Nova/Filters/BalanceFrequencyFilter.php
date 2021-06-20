<?php

declare(strict_types=1);

namespace App\Nova\Filters;

use App\Enums\Models\Billing\BalanceFrequency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

/**
 * Class BalanceFrequencyFilter.
 */
class BalanceFrequencyFilter extends Filter
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
    public function name(): array | string | null
    {
        return __('nova.frequency');
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
        return $query->where('frequency', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param Request $request
     * @return array
     */
    public function options(Request $request): array
    {
        return array_flip(BalanceFrequency::asSelectArray());
    }
}
