<?php

declare(strict_types=1);

namespace App\Events\List\Playlist\Track;

use App\Events\Base\List\ListDeletedEvent;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Str;

/**
 * Class TrackDeleted.
 *
 * @extends ListDeletedEvent<PlaylistTrack>
 */
class TrackDeleted extends ListDeletedEvent
{
    /**
     * The playlist the track belongs to.
     *
     * @var Playlist|null
     */
    protected ?Playlist $playlist;

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
        return Str::of("Track '**{$this->getModel()->getName()}**' has been deleted")
            ->append($this->playlist ? " for Playlist '**{$this->playlist->getName()}**'." : '.')
            ->__toString();
    }
}
