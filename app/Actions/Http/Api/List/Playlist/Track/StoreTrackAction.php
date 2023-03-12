<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\Playlist\InsertTrackAction;
use App\Actions\Models\List\Playlist\InsertTrackAfterAction;
use App\Actions\Models\List\Playlist\InsertTrackBeforeAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
     * @return Model
     *
     * @throws Exception
     */
    public function store(Playlist $playlist, Builder $builder, array $parameters): Model
    {
        $trackParameters = $parameters;

        $previousId = Arr::pull($trackParameters, PlaylistTrack::ATTRIBUTE_PREVIOUS);
        $nextId = Arr::pull($trackParameters, PlaylistTrack::ATTRIBUTE_NEXT);

        $trackParameters = $trackParameters + [PlaylistTrack::ATTRIBUTE_PLAYLIST => $playlist->getKey()];

        Log::debug('Track Parameters', $trackParameters);

        try {
            DB::beginTransaction();

            Log::debug('Begin Store Transaction');

            $storeAction = new StoreAction();

            /** @var PlaylistTrack $track */
            $track = $storeAction->store($builder, $trackParameters);

            if (! empty($nextId) && empty($previousId)) {
                /** @var PlaylistTrack $next */
                $next = PlaylistTrack::query()
                    ->with(PlaylistTrack::RELATION_PREVIOUS)
                    ->findOrFail($nextId);

                Log::debug('Next Track', $next->toArray());

                $insertAction = new InsertTrackBeforeAction();

                $insertAction->insertBefore($playlist, $track, $next);

                Log::debug('Insert Before Completed');
            }

            if (! empty($previousId) && empty($nextId)) {
                /** @var PlaylistTrack $previous */
                $previous = PlaylistTrack::query()
                    ->with(PlaylistTrack::RELATION_NEXT)
                    ->findOrFail($previousId);

                Log::debug('Previous Track', $previous->toArray());

                $insertAction = new InsertTrackAfterAction();

                $insertAction->insertAfter($playlist, $track, $previous);

                Log::debug('Insert After Completed');
            }

            if (empty($nextId) && empty($previousId)) {
                $insertAction = new InsertTrackAction();

                $insertAction->insert($playlist, $track);

                Log::debug('Insert Completed');
            }

            DB::commit();

            Log::debug('End Store Transaction');

            return $storeAction->cleanup($track);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
    }
}
