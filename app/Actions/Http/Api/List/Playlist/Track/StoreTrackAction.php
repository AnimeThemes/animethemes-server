<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\Playlist\InsertTrackAction;
use App\Actions\Models\List\Playlist\InsertTrackAfterAction;
use App\Actions\Models\List\Playlist\InsertTrackBeforeAction;
use App\Concerns\Actions\List\LocksPlaylist;
use App\Contracts\Models\HasHashids;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreTrackAction
{
    use LocksPlaylist;

    /**
     * @throws Exception
     */
    public function store(Playlist $playlist, Builder $builder, array $parameters): PlaylistTrack
    {
        return $this->withPlaylistLock($playlist, function () use ($playlist, $builder, $parameters) {
            $trackParameters = $parameters;

            $previousHashid = Arr::pull($trackParameters, PlaylistTrack::RELATION_PREVIOUS);
            $nextHashid = Arr::pull($trackParameters, PlaylistTrack::RELATION_NEXT);

            $trackParameters += [PlaylistTrack::ATTRIBUTE_PLAYLIST => $playlist->getKey()];

            try {
                DB::beginTransaction();

                /** @var StoreAction<PlaylistTrack> $storeAction */
                $storeAction = new StoreAction();

                $track = $storeAction->store($builder, $trackParameters);

                if (filled($nextHashid) && blank($previousHashid)) {
                    /** @var PlaylistTrack $next */
                    $next = PlaylistTrack::query()
                        ->with(PlaylistTrack::RELATION_PREVIOUS)
                        ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey())
                        ->where(HasHashids::ATTRIBUTE_HASHID, $nextHashid)
                        ->firstOrFail();

                    $insertAction = new InsertTrackBeforeAction();

                    $insertAction->insertBefore($playlist, $track, $next);
                }

                if (filled($previousHashid) && blank($nextHashid)) {
                    /** @var PlaylistTrack $previous */
                    $previous = PlaylistTrack::query()
                        ->with(PlaylistTrack::RELATION_NEXT)
                        ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey())
                        ->where(HasHashids::ATTRIBUTE_HASHID, $previousHashid)
                        ->firstOrFail();

                    $insertAction = new InsertTrackAfterAction();

                    $insertAction->insertAfter($playlist, $track, $previous);
                }

                if (blank($nextHashid) && blank($previousHashid)) {
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
        });
    }
}
