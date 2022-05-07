<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Artist;

/**
 * Class ArtistCreated.
 *
 * @extends WikiCreatedEvent<Artist>
 */
class ArtistCreated extends WikiCreatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Artist  $artist
     */
    public function __construct(Artist $artist)
    {
        parent::__construct($artist);
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
        return "Artist '**{$this->getModel()->getName()}**' has been created.";
    }
}
