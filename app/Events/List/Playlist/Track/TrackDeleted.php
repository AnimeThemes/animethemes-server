<?php

declare(strict_types=1);

namespace App\Events\List\Playlist\Track;

use App\Events\Base\List\ListDeletedEvent;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Str;

/**
 * @extends ListDeletedEvent<PlaylistTrack>
 */
class TrackDeleted extends ListDeletedEvent
{
    /**
     * The playlist the track belongs to.
     */
    protected ?Playlist $playlist;

    public function __construct(PlaylistTrack $track)
    {
        parent::__construct($track);
        $this->playlist = $track->playlist;
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
        return Str::of("Track '**{$this->getModel()->getName()}**' has been deleted")
            ->append($this->playlist ? " for Playlist '**{$this->playlist->getName()}**'." : '.')
            ->__toString();
    }
}
