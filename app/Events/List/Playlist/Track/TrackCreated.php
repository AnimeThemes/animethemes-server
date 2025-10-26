<?php

declare(strict_types=1);

namespace App\Events\List\Playlist\Track;

use App\Contracts\Events\AssignHashidsEvent;
use App\Events\Base\List\ListCreatedEvent;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * @extends ListCreatedEvent<PlaylistTrack>
 */
class TrackCreated extends ListCreatedEvent implements AssignHashidsEvent
{
    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Track '**{$this->getModel()->getName()}**' has been created for Playlist '**{$this->getModel()->playlist->getName()}**'.";
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
