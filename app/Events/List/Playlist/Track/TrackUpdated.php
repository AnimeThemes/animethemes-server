<?php

declare(strict_types=1);

namespace App\Events\List\Playlist\Track;

use App\Events\Base\List\ListUpdatedEvent;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class TrackUpdated.
 *
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

    /**
     * Determine if the message should be sent.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): PlaylistTrack
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Track '**{$this->getModel()->getName()}**' has been updated for Playlist '**{$this->playlist->getName()}**'.";
    }
}
