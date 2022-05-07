<?php

declare(strict_types=1);

namespace App\Events\Wiki\Series;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Series;

/**
 * Class SeriesUpdated.
 *
 * @extends WikiUpdatedEvent<Series>
 */
class SeriesUpdated extends WikiUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Series  $series
     */
    public function __construct(Series $series)
    {
        parent::__construct($series);
        $this->initializeEmbedFields($series);
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
        return "Series '**{$this->getModel()->getName()}**' has been updated.";
    }
}
