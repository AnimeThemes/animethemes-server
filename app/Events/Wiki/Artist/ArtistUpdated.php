<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Artist;

/**
 * Class ArtistUpdated.
 *
 * @extends WikiUpdatedEvent<Artist>
 */
class ArtistUpdated extends WikiUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Artist  $artist
     */
    public function __construct(Artist $artist)
    {
        parent::__construct($artist);
        $this->initializeEmbedFields($artist);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Artist
     */
    public function getModel(): Artist
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
        return "Artist '**{$this->getModel()->getName()}**' has been updated.";
    }
}
