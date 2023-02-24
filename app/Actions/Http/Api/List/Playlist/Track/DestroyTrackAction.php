<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Model;

class DestroyTrackAction
{
    /**
     * Destroy playlist track.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return Model
     */
    public function destroy(Playlist $playlist, PlaylistTrack $track): Model
    {
        $removeAction = new RemoveTrackAction();

        $removeAction->remove($playlist, $track);

        $destroyAction = new DestroyAction();

        return $destroyAction->destroy($track);
    }
}
