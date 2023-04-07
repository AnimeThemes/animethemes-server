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

            Log::info('InsertTrackAction start');

            if ($playlist->first()->doesntExist()) {
                Log::info('InsertTrackAction playlist first');

                $playlist->first()->associate($track)->save();
            }

            $last = $playlist->last;

            Log::info('InsertTrackAction playlist last');

            $playlist->last()->associate($track)->save();

            Log::info('InsertTrackAction track previous');

            $last?->next()?->associate($track)?->save();
            $track->previous()->associate($last);

            Log::info('InsertTrackAction track next');

            $track->next()->disassociate()->save();

            DB::commit();

            Log::info('InsertTrackAction committed');
        } catch (Exception $e) {
            Log::error('InsertTrackAction exception caught');

            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
