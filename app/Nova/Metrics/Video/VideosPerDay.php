<?php

declare(strict_types=1);

namespace App\Nova\Metrics\Video;

use App\Models\Wiki\Video;
use DateInterval;
use DateTimeInterface;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

/**
 * Class VideosPerDay.
 */
class VideosPerDay extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  NovaRequest  $request
     * @return TrendResult
     */
    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->countByDays($request, Video::class);
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
            30 => __('nova.metrics.ranges.trend.30'),
            60 => __('nova.metrics.ranges.trend.60'),
            90 => __('nova.metrics.ranges.trend.90'),
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
        return 'videos-per-day';
    }
}
