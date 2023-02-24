<?php

declare(strict_types=1);

namespace App\Actions\Models\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class InsertTrackAfterAction.
 */
class InsertTrackAfterAction
{
    /**
     * Insert track after previous track.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  PlaylistTrack  $previous
     * @return void
     */
    public function insertAfter(Playlist $playlist, PlaylistTrack $track, PlaylistTrack $previous): void
    {
        if ($playlist->last()->is($previous)) {
            $playlist->last()->associate($track)->save();
        }

        $next = $previous->next;
        $next?->previous()?->associate($track)?->save();
        $track->next()->associate($next);

        $previous->next()->associate($track)->save();
        $track->previous()->associate($previous)->save();
    }
}
