<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\StoreAction;
use App\Actions\Models\List\Playlist\InsertTrackAction;
use App\Actions\Models\List\Playlist\InsertTrackAfterAction;
use App\Actions\Models\List\Playlist\InsertTrackBeforeAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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
     */
    public function store(Playlist $playlist, Builder $builder, array $parameters): Model
    {
        $trackParameters = $parameters;

        $previousId = Arr::pull($trackParameters, PlaylistTrack::ATTRIBUTE_PREVIOUS);
        $nextId = Arr::pull($trackParameters, PlaylistTrack::ATTRIBUTE_NEXT);

        $trackParameters = $trackParameters + [PlaylistTrack::ATTRIBUTE_PLAYLIST => $playlist->getKey()];

        $storeAction = new StoreAction();

        /** @var PlaylistTrack $track */
        $track = $storeAction->store($builder, $trackParameters);

        if (! empty($nextId) && empty($previousId)) {
            /** @var PlaylistTrack $next */
            $next = PlaylistTrack::query()
                ->with(PlaylistTrack::RELATION_PREVIOUS)
                ->findOrFail($nextId);

            $insertAction = new InsertTrackBeforeAction();

            $insertAction->insertBefore($playlist, $track, $next);
        }

        if (! empty($previousId) && empty($nextId)) {
            /** @var PlaylistTrack $previous */
            $previous = PlaylistTrack::query()
                ->with(PlaylistTrack::RELATION_NEXT)
                ->findOrFail($previousId);

            $insertAction = new InsertTrackAfterAction();

            $insertAction->insertAfter($playlist, $track, $previous);
        }

        if (empty($nextId) && empty($previousId)) {
            $insertAction = new InsertTrackAction();

            $insertAction->insert($playlist, $track);
        }

        return $storeAction->cleanup($track);
    }
}
