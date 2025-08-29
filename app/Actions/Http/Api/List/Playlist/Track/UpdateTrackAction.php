<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\UpdateAction;
use App\Actions\Models\List\Playlist\InsertTrackAfterAction;
use App\Actions\Models\List\Playlist\InsertTrackBeforeAction;
use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Contracts\Models\HasHashids;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateTrackAction
{
    /**
     * @param  array  $parameters
     *
     * @throws Exception
     */
    public function update(Playlist $playlist, PlaylistTrack $track, array $parameters): PlaylistTrack
    {
        $trackParameters = $parameters;

        $previousHashid = Arr::pull($trackParameters, PlaylistTrack::RELATION_PREVIOUS);
        $nextHashid = Arr::pull($trackParameters, PlaylistTrack::RELATION_NEXT);

        try {
            DB::beginTransaction();

            // Lock tracks to prevent race conditions.
            Playlist::query()->whereKey($playlist->getKey())->lockForUpdate()->first();
            $playlist->tracks()->getQuery()->lockForUpdate()->count();

            /** @var UpdateAction<PlaylistTrack> $updateAction */
            $updateAction = new UpdateAction();

            $updateAction->update($track, $trackParameters);

            if (! empty($nextHashid) || ! empty($previousHashid)) {
                $removeAction = new RemoveTrackAction();

                $removeAction->remove($playlist, $track);
            }

            if (! empty($nextHashid) && empty($previousHashid)) {
                $next = PlaylistTrack::query()
                    ->with(PlaylistTrack::RELATION_PREVIOUS)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey())
                    ->where(HasHashids::ATTRIBUTE_HASHID, $nextHashid)
                    ->firstOrFail();

                $insertAction = new InsertTrackBeforeAction();

                $insertAction->insertBefore($playlist, $track, $next);
            }

            if (! empty($previousHashid) && empty($nextHashid)) {
                $previous = PlaylistTrack::query()
                    ->with(PlaylistTrack::RELATION_NEXT)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey())
                    ->where(HasHashids::ATTRIBUTE_HASHID, $previousHashid)
                    ->firstOrFail();

                $insertAction = new InsertTrackAfterAction();

                $insertAction->insertAfter($playlist, $track, $previous);
            }

            DB::commit();

            return $updateAction->cleanup($track);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
