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
     * Append track to playlist.
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
        $last?->next()?->associate($track)?->save();
        $track->previous()->associate($last);

        $track->next()->disassociate();

        if ($track->isDirty()) {
            $track->save();
        }

        $playlist->last()->associate($track)->save();
    }
}
