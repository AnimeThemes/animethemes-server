<?php

declare(strict_types=1);

namespace App\Nova\Filters\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

/**
 * Class AnimeSeasonFilter.
 */
class AnimeSeasonFilter extends Filter
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.season');
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
        return $query->where('season', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  Request  $request
     * @return array
     */
    public function options(Request $request): array
    {
        return array_flip(AnimeSeason::asSelectArray());
    }
}
