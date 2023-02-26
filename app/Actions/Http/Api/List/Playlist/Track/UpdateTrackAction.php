<?php

declare(strict_types=1);

namespace App\Actions\Http\Api\List\Playlist\Track;

use App\Actions\Http\Api\UpdateAction;
use App\Actions\Models\List\Playlist\InsertTrackAfterAction;
use App\Actions\Models\List\Playlist\InsertTrackBeforeAction;
use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Class UpdateTrackAction.
 */
class UpdateTrackAction
{
    /**
     * Store playlist track.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  array  $parameters
     * @return Model
     */
    public function update(Playlist $playlist, PlaylistTrack $track, array $parameters): Model
    {
        $trackParameters = $parameters;

        $previousId = Arr::pull($trackParameters, PlaylistTrack::ATTRIBUTE_PREVIOUS);
        $nextId = Arr::pull($trackParameters, PlaylistTrack::ATTRIBUTE_NEXT);

        $updateAction = new UpdateAction();

        $updateAction->update($track, $trackParameters);

        if (! empty($nextId) || ! empty($previousId)) {
            $removeAction = new RemoveTrackAction();

            $removeAction->remove($playlist, $track);
        }

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

        return $updateAction->cleanup($track);
    }
}
