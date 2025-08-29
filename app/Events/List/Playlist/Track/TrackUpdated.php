<?php

declare(strict_types=1);

namespace App\Events\List\Playlist\Track;

use App\Events\Base\List\ListUpdatedEvent;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * @extends ListUpdatedEvent<PlaylistTrack>
 */
class TrackUpdated extends ListUpdatedEvent
{
    /**
     * The playlist the track belongs to.
     */
    protected Playlist $playlist;

    public function __construct(PlaylistTrack $track)
    {
        parent::__construct($track);
        $this->playlist = $track->playlist;
        $this->initializeEmbedFields($track);
    }

    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    public function getModel(): PlaylistTrack
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Track '**{$this->getModel()->getName()}**' has been updated for Playlist '**{$this->playlist->getName()}**'.";
    }
}
