<?php

declare(strict_types=1);

namespace App\Events\List\Playlist;

use App\Contracts\Events\AssignHashidsEvent;
use App\Events\Base\List\ListCreatedEvent;
use App\Models\List\Playlist;

/**
 * @extends ListCreatedEvent<Playlist>
 */
class PlaylistCreated extends ListCreatedEvent implements AssignHashidsEvent
{
    public function __construct(Playlist $playlist)
    {
        parent::__construct($playlist);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Playlist
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Playlist '**{$this->getModel()->getName()}**' has been created.";
    }

    /**
     * Get the Hashids connection.
     *
     * @return string
     */
    public function getHashidsConnection(): ?string
    {
        return 'playlists';
    }
}
