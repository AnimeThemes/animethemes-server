<?php

declare(strict_types=1);

namespace App\Actions\Models\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class RemoveTrackAction.
 */
class RemoveTrackAction
{
    /**
     * Remove track from playlist.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return void
     */
    public function remove(Playlist $playlist, PlaylistTrack $track): void
    {
        $previous = $track->previous;
        $next = $track->next;

        if ($playlist->first()->is($track)) {
            $playlist->first()->associate($next);
        }
        if ($playlist->last()->is($track)) {
            $playlist->last()->associate($previous);
        }
        if ($playlist->isDirty()) {
            $playlist->save();
        }

        $previous?->next()?->associate($next)?->save();
        $next?->previous()?->associate($previous)?->save();

        $track->previous()->disassociate();
        $track->next()->disassociate()->save();
    }
}
