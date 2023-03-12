<?php

declare(strict_types=1);

namespace App\Actions\Models\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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
     */
    public function insert(Playlist $playlist, PlaylistTrack $track): void
    {
        try {
            DB::beginTransaction();

            Log::debug('Begin Transaction');

            if ($playlist->first()->doesntExist()) {
                $playlist->first()->associate($track);
            }

            Log::debug('First Playlist Track');

            $last = $playlist->last;
            $last?->next()?->associate($track)?->save();
            $track->previous()->associate($last);

            Log::debug('Track Last Relation');

            $track->next()->disassociate();

            Log::debug('Track Next Relation');

            if ($track->isDirty()) {
                $track->save();
            }

            Log::debug('Track Saved');

            $playlist->last()->associate($track)->save();

            Log::debug('Playlist last relation');

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
        }
    }
}
