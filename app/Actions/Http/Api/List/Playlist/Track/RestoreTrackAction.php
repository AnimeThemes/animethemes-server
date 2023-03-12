<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\RestoreAction;
use App\Actions\Models\List\Playlist\InsertTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RestoreTrackAction.
 */
class RestoreTrackAction
{
    /**
     * Store playlist track.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return Model
     *
     * @throws Exception
     */
    public function restore(Playlist $playlist, PlaylistTrack $track): Model
    {
        $restoreAction = new RestoreAction();

        $restoreAction->restore($track);

        $insertAction = new InsertTrackAction();

        $insertAction->insert($playlist, $track);

        return $restoreAction->cleanup($track);
    }
}
