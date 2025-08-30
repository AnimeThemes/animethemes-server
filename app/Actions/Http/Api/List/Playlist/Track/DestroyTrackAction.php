<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DestroyTrackAction
{
    /**
     * @throws Exception
     */
    public function destroy(Playlist $playlist, PlaylistTrack $track): string
    {
        try {
            DB::beginTransaction();

            // Lock tracks to prevent race conditions.
            Playlist::query()->whereKey($playlist->getKey())->lockForUpdate()->first();
            $playlist->tracks()->getQuery()->lockForUpdate()->count();

            $removeAction = new RemoveTrackAction();

            $removeAction->remove($playlist, $track);

            $message = Str::of(Str::headline(class_basename($track)))
                ->append(' \'')
                ->append($track->getName())
                ->append('\' was deleted.')
                ->__toString();

            $destroyAction = new DestroyAction();

            $destroyAction->forceDelete($track);

            DB::commit();

            return $message;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
