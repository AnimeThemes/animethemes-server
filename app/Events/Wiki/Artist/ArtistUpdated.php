<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Artist;

/**
 * @extends WikiUpdatedEvent<Artist>
 */
class ArtistUpdated extends WikiUpdatedEvent
{
    public function __construct(Artist $artist)
    {
        parent::__construct($artist);
        $this->initializeEmbedFields($artist);
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
        return "Artist '**{$this->getModel()->getName()}**' has been updated.";
    }
}
