<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

class ForceDeleteTrackAction
{
    /**
     * Force delete playlist track.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return string
     */
    public function forceDelete(Playlist $playlist, PlaylistTrack $track): string
    {
        $removeAction = new RemoveTrackAction();

        $removeAction->remove($playlist, $track);

        $forceDeleteAction = new ForceDeleteAction();

        return $forceDeleteAction->forceDelete($track);
    }
}
