<?php

declare(strict_types=1);

namespace App\Events\List\Playlist\Track;

use App\Contracts\Events\AssignHashidsEvent;
use App\Events\Base\List\ListCreatedEvent;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class TrackCreated.
 *
 * @extends ListCreatedEvent<PlaylistTrack>
 */
class TrackCreated extends ListCreatedEvent implements AssignHashidsEvent
{
    /**
     * The playlist the track belongs to.
     *
     * @var Playlist
     */
    protected Playlist $playlist;

    /**
     * Create a new event instance.
     *
     * @param  PlaylistTrack  $track
     */
    public function __construct(PlaylistTrack $track)
    {
        parent::__construct($track);
        $this->playlist = $track->playlist;
    }

    /**
     * Determine if the message should be sent.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    /**
     * Get the model that has fired this event.
     *
     * @return PlaylistTrack
     */
    public function getModel(): PlaylistTrack
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
        return "Track '**{$this->getModel()->getName()}**' has been created for Playlist '**{$this->playlist->getName()}**'.";
    }

    /**
     * Get the Hashids connection.
     *
     * @return string|null
     */
    public function getHashidsConnection(): ?string
    {
        return 'playlists';
    }
}
