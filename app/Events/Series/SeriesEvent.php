<?php

namespace App\Events\Series;

use App\Models\Series;

abstract class SeriesEvent
{
    /**
     * The series that has fired this event.
     *
     * @var \App\Models\Series
     */
    protected $series;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Series $series
     * @return void
     */
    public function __construct(Series $series)
    {
        $this->series = $series;
    }

    /**
     * Get the series that has fired this event.
     *
     * @return \App\Models\Series
     */
    public function getSeries()
    {
        return $this->series;
    }
}
