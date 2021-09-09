<?php

declare(strict_types=1);

namespace App\Nova\Filters\Wiki\Video;

use App\Enums\Models\Wiki\VideoSource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

/**
 * Class VideoSourceFilter.
 */
class VideoSourceFilter extends Filter
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
        return __('nova.source');
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
        return $query->where('source', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  Request  $request
     * @return array
     */
    public function options(Request $request): array
    {
        return array_flip(VideoSource::asSelectArray());
    }
}
