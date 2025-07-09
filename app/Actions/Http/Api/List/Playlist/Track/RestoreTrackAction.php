<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\RestoreAction;
use App\Actions\Models\List\Playlist\InsertTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        try {
            DB::beginTransaction();

            // Lock tracks to prevent race conditions.
            Playlist::query()->whereKey($playlist->getKey())->lockForUpdate()->first();
            $playlist->tracks()->getQuery()->lockForUpdate()->count();

            $restoreAction = new RestoreAction();

            $restoreAction->restore($track);

            $insertAction = new InsertTrackAction();

            $insertAction->insert($playlist, $track);

            $restored = $restoreAction->cleanup($track);

            DB::commit();

            return $restored;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
