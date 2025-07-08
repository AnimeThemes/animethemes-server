<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class DestroyTrackAction.
 */
class DestroyTrackAction
{
    /**
     * Destroy playlist track.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return Model
     *
     * @throws Exception
     */
    public function destroy(Playlist $playlist, PlaylistTrack $track): Model
    {
        try {
            DB::beginTransaction();

            // Lock tracks to prevent race conditions.
            Playlist::query()->whereKey($playlist->getKey())->lockForUpdate()->first();
            $playlist->tracks()->getQuery()->lockForUpdate()->count();

            $removeAction = new RemoveTrackAction();

            $removeAction->remove($playlist, $track);

            $destroyAction = new DestroyAction();

            $destroyed = $destroyAction->destroy($track);

            DB::commit();

            return $destroyed;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
