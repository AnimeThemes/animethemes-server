<?php

declare(strict_types=1);

namespace App\Actions\Models\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     *
     * @throws Exception
     */
    public function insertBefore(Playlist $playlist, PlaylistTrack $track, PlaylistTrack $next): void
    {
        try {
            DB::beginTransaction();

            if ($playlist->first()->is($next)) {
                $playlist->first()->associate($track)->save();
            }

            $previous = $next->previous;
            $previous?->next()?->associate($track)?->save();
            $track->previous()->associate($previous);

            $next->previous()->associate($track)->save();
            $track->next()->associate($next)->save();

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
