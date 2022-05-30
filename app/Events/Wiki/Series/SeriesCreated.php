<?php

declare(strict_types=1);

namespace App\Events\Wiki\Series;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Series;

/**
 * Class SeriesCreated.
 *
 * @extends WikiCreatedEvent<Series>
 */
class SeriesCreated extends WikiCreatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Series  $series
     */
    public function __construct(Series $series)
    {
        parent::__construct($series);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Series
     */
    public function getModel(): Series
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Series '**{$this->getModel()->getName()}**' has been created.";
    }
}
