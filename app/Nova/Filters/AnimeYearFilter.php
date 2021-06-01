<?php

declare(strict_types=1);

namespace App\Nova\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Filters\Filter;

/**
 * Class AnimeYearFilter
 * @package App\Nova\Filters
 */
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
     * @return array|string|null
     */
    public function name(): array|string|null
    {
        return __('nova.year');
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
        return $query->where('year', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param Request $request
     * @return array
     */
    public function options(Request $request): array
    {
        $options = [];

        for ($year = 1960; $year <= intval(date('Y')) + 1; $year++) {
            $options = Arr::add($options, strval($year), strval($year));
        }

        return $options;
    }
}
