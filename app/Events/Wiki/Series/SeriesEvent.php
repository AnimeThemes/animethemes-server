<?php

declare(strict_types=1);

namespace App\Events\Wiki\Series;

use App\Models\Wiki\Series;

/**
 * Class SeriesEvent.
 */
abstract class SeriesEvent
{
    /**
     * The series that has fired this event.
     *
     * @var Series
     */
    protected Series $series;

    /**
     * Create a new event instance.
     *
     * @param  Series  $series
     * @return void
     */
    public function __construct(Series $series)
    {
        $this->series = $series;
    }

    /**
     * Get the series that has fired this event.
     *
     * @return Series
     */
    public function getSeries(): Series
    {
        return $this->series;
    }
}
