<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Artist;

/**
 * Class ArtistRestored.
 *
 * @extends WikiRestoredEvent<Artist>
 */
class ArtistRestored extends WikiRestoredEvent
{
    public function __construct(Artist $artist)
    {
        parent::__construct($artist);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Artist
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Artist '**{$this->getModel()->getName()}**' has been restored.";
    }
}
