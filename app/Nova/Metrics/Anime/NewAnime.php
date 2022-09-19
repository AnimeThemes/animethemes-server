<?php

declare(strict_types=1);

namespace App\Nova\Metrics\Anime;

use App\Models\Wiki\Anime;
use DateInterval;
use DateTimeInterface;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

/**
 * Class NewAnime.
 */
class NewAnime extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  NovaRequest  $request
     * @return ValueResult
     */
    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->count($request, Anime::class);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function ranges(): array
    {
        return [
            30 => __('nova.metrics.ranges.value.30'),
            60 => __('nova.metrics.ranges.value.60'),
            365 => __('nova.metrics.ranges.value.365'),
            'TODAY' => __('nova.metrics.ranges.value.today'),
            'YESTERDAY' => __('nova.metrics.ranges.value.yesterday'),
            'MTD' => __('nova.metrics.ranges.value.mtd'),
            'QTD' => __('nova.metrics.ranges.value.qtd'),
            'YTD' => __('nova.metrics.ranges.value.ytd'),
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return DateTimeInterface|DateInterval|float|int
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function cacheFor(): DateInterval|float|DateTimeInterface|int
    {
        return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function uriKey(): string
    {
        return 'new-anime';
    }
}
