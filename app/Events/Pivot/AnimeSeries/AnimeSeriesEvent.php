<?php

declare(strict_types=1);

namespace App\Events\Pivot\AnimeSeries;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\AnimeSeries;

/**
 * Class AnimeSeriesEvent.
 */
abstract class AnimeSeriesEvent
{
    /**
     * The anime that this anime series belongs to.
     *
     * @var Anime
     */
    protected Anime $anime;

    /**
     * The series that this anime series belongs to.
     *
     * @var Series
     */
    protected Series $series;

    /**
     * Create a new event instance.
     *
     * @param AnimeSeries $animeSeries
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
     * @return Anime
     */
    public function getAnime(): Anime
    {
        return $this->anime;
    }

    /**
     * Get the series that this anime series belongs to.
     *
     * @return Series
     */
    public function getSeries(): Series
    {
        return $this->series;
    }
}
