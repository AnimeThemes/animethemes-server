<?php

declare(strict_types=1);

namespace App\Actions\Models\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class InsertTrackBeforeAction.
 */
class InsertTrackBeforeAction
{
    /**
     * Insert track before next track.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  PlaylistTrack  $next
     * @return void
     */
    public function insertBefore(Playlist $playlist, PlaylistTrack $track, PlaylistTrack $next): void
    {
        if ($playlist->first()->is($next)) {
            $playlist->first()->associate($track)->save();
        }

        $previous = $next->previous;
        $previous?->next()?->associate($track)?->save();
        $track->previous()->associate($previous);

        $next->previous()->associate($track)->save();
        $track->next()->associate($next)->save();
    }
}
