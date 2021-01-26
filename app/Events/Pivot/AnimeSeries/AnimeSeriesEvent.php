<?php

namespace App\Events\Pivot\AnimeSeries;

use App\Pivots\AnimeSeries;

abstract class AnimeSeriesEvent
{
    /**
     * The anime that this anime series belongs to.
     *
     * @var \App\Models\Anime
     */
    protected $anime;

    /**
     * The series that has fired this event.
     *
     * @var \App\Models\Series
     */
    protected $series;

    /**
     * Create a new event instance.
     *
     * @param @var \App\Pivots\AnimeSeries $animeSeries
     * @return void
     */
    public function __construct(AnimeSeries $animeSeries)
    {
        $this->anime = $animeSeries->anime;
        $this->series = $animeSeries->series;
    }

    /**
     * Get the anime that this anime series belongs to.
     *
     * @return \App\Models\Anime
     */
    public function getAnime()
    {
        return $this->anime;
    }

    /**
     * Get the anime that this anime series belongs to.
     *
     * @return \App\Models\Series
     */
    public function getSeries()
    {
        return $this->series;
    }
}
