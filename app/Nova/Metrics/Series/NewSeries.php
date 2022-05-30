<?php

declare(strict_types=1);

namespace App\Nova\Metrics\Series;

use App\Models\Wiki\Series;
use DateInterval;
use DateTimeInterface;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

/**
 * Class NewSeries.
 */
class NewSeries extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  NovaRequest  $request
     * @return ValueResult
     */
    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->count($request, Series::class);
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
            30 => '30 Days',
            60 => '60 Days',
            365 => '365 Days',
            'TODAY' => 'Today',
            'YESTERDAY' => 'Yesterday',
            'MTD' => 'Month To Date',
            'QTD' => 'Quarter To Date',
            'YTD' => 'Year To Date',
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
        return 'new-series';
    }
}
