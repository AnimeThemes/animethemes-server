<?php

declare(strict_types=1);

namespace App\Actions\Models\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class InsertTrackAction.
 */
class InsertTrackAction
{
    /**
     * Insert new track before next track.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return void
     */
    public function insert(Playlist $playlist, PlaylistTrack $track): void
    {
        if ($playlist->first()->doesntExist()) {
            $playlist->first()->associate($track);
        }

        $last = $playlist->last;

        if ($last !== null) {
            $last->next()->associate($track)->save();
            $track->previous()->associate($last)->save();
        }

        $playlist->last()->associate($track)->save();
    }
}
