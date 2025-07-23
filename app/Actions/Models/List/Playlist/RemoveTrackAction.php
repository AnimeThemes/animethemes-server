<?php

declare(strict_types=1);

namespace App\Actions\Models\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoveTrackAction
{
    /**
     * Remove track from playlist.
     *
     * @throws Exception
     */
    public function remove(Playlist $playlist, PlaylistTrack $track): void
    {
        try {
            DB::beginTransaction();

            $previous = $track->previous;
            $next = $track->next;

            if ($playlist->first()->is($track)) {
                $playlist->first()->associate($next);
            }
            if ($playlist->last()->is($track)) {
                $playlist->last()->associate($previous);
            }
            $playlist->save();

            $previous?->next()?->associate($next)?->save();
            $next?->previous()?->associate($previous)?->save();

            $track->previous()->disassociate();
            $track->next()->disassociate()->save();

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
