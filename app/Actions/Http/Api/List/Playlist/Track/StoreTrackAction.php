<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\Playlist\InsertTrackAction;
use App\Actions\Models\List\Playlist\InsertTrackAfterAction;
use App\Actions\Models\List\Playlist\InsertTrackBeforeAction;
use App\Contracts\Models\HasHashids;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class StoreTrackAction.
 */
class StoreTrackAction
{
    /**
     * Store playlist track.
     *
     * @param  Playlist  $playlist
     * @param  Builder  $builder
     * @param  array  $parameters
     * @return PlaylistTrack
     *
     * @throws Exception
     */
    public function store(Playlist $playlist, Builder $builder, array $parameters): PlaylistTrack
    {
        $trackParameters = $parameters;

        $previousHashid = Arr::pull($trackParameters, PlaylistTrack::RELATION_PREVIOUS);
        $nextHashid = Arr::pull($trackParameters, PlaylistTrack::RELATION_NEXT);

        $trackParameters = $trackParameters + [PlaylistTrack::ATTRIBUTE_PLAYLIST => $playlist->getKey()];

        try {
            DB::beginTransaction();

            // Lock tracks to prevent race conditions.
            Playlist::query()->whereKey($playlist->getKey())->lockForUpdate()->first();
            $playlist->tracks()->getQuery()->lockForUpdate()->count();

            /** @var StoreAction<PlaylistTrack> $storeAction */
            $storeAction = new StoreAction();

            $track = $storeAction->store($builder, $trackParameters);

            if (! empty($nextHashid) && empty($previousHashid)) {
                /** @var PlaylistTrack $next */
                $next = PlaylistTrack::query()
                    ->with(PlaylistTrack::RELATION_PREVIOUS)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey())
                    ->where(HasHashids::ATTRIBUTE_HASHID, $nextHashid)
                    ->firstOrFail();

                $insertAction = new InsertTrackBeforeAction();

                $insertAction->insertBefore($playlist, $track, $next);
            }

            if (! empty($previousHashid) && empty($nextHashid)) {
                /** @var PlaylistTrack $previous */
                $previous = PlaylistTrack::query()
                    ->with(PlaylistTrack::RELATION_NEXT)
                    ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey())
                    ->where(HasHashids::ATTRIBUTE_HASHID, $previousHashid)
                    ->firstOrFail();

                $insertAction = new InsertTrackAfterAction();

                $insertAction->insertAfter($playlist, $track, $previous);
            }

            if (empty($nextHashid) && empty($previousHashid)) {
                $insertAction = new InsertTrackAction();

                $insertAction->insert($playlist, $track);
            }

            DB::commit();

            return $storeAction->cleanup($track);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
