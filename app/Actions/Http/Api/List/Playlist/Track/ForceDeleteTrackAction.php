<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ForceDeleteTrackAction.
 */
class ForceDeleteTrackAction
{
    /**
     * Force delete playlist track.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return string
     *
     * @throws Exception
     */
    public function forceDelete(Playlist $playlist, PlaylistTrack $track): string
    {
        try {
            DB::beginTransaction();

            $removeAction = new RemoveTrackAction();

            $removeAction->remove($playlist, $track);

            $forceDeleteAction = new ForceDeleteAction();

            $message = $forceDeleteAction->forceDelete($track);

            DB::commit();

            return $message;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
