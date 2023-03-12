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

            Log::debug('Begin Playlist Transaction');

            if ($playlist->first()->doesntExist()) {
                $playlist->first()->associate($track);
            }

            Log::debug('First Playlist Track Set');

            $this->insertTrack($playlist, $track);

            Log::debug('Insert Track Completed');

            $playlist->last()->associate($track)->save();

            Log::debug('First Playlist Track Set');

            DB::commit();

            Log::debug('End Playlist Transaction');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Set Track Relation with nested transaction.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return void
     *
     * @throws Exception
     */
    private function insertTrack(Playlist $playlist, PlaylistTrack $track): void
    {
        try {
            DB::beginTransaction();

            Log::debug('Begin Track Transaction');

            $last = $playlist->last;
            $last?->next()?->associate($track)?->save();
            $track->previous()->associate($last);

            Log::debug('Set Previous Track Relation');

            $track->next()->disassociate();

            Log::debug('Set Next Track Relation');

            if ($track->isDirty()) {
                $track->save();
            }

            Log::debug('Track Saved');

            DB::commit();

            Log::debug('End Track Transaction');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
