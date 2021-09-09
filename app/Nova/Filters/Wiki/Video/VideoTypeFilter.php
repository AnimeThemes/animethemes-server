<?php

declare(strict_types=1);

namespace App\Nova\Filters\Wiki\Video;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

/**
 * Class VideoTypeFilter.
 */
class VideoTypeFilter extends Filter
{
    public const ANIME = 'anime';
    public const MISC = 'misc';

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
        return __('nova.type');
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
        return match ($value) {
            self::ANIME => $query->where('path', 'not like', 'misc%'),
            self::MISC => $query->where('path', 'like', 'misc%'),
            default => $query,
        };
    }

    /**
     * Get the filter's available options.
     *
     * @param  Request  $request
     * @return array
     */
    public function options(Request $request): array
    {
        return [
            __('nova.anime') => self::ANIME,
            __('nova.misc') => self::MISC,
        ];
    }
}
