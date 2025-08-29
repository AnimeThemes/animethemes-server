<?php

declare(strict_types=1);

namespace App\Events\Wiki\Series;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Series;

/**
 * @extends WikiCreatedEvent<Series>
 */
class SeriesCreated extends WikiCreatedEvent
{
    public function __construct(Series $series)
    {
        parent::__construct($series);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Series
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Series '**{$this->getModel()->getName()}**' has been created.";
    }
}
