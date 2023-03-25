<?php

declare(strict_types=1);

namespace App\Actions\Models\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     *
     * @throws Exception
     */
    public function insert(Playlist $playlist, PlaylistTrack $track): void
    {
        try {
            DB::beginTransaction();

            if ($playlist->first()->doesntExist()) {
                $playlist->first()->associate($track)->save();
            }

            $last = $playlist->last;

            $playlist->last()->associate($track)->save();

            $last?->next()?->associate($track)?->save();
            $track->previous()->associate($last);

            $track->next()->disassociate()->save();

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
