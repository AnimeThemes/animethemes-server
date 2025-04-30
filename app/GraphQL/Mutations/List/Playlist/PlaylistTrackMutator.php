<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\List\Playlist;

use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\ForceDeleteTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\RestoreTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\StoreTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\UpdateTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

/**
 * Class PlaylistTrackMutator.
 */
class PlaylistTrackMutator
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Store a newly created resource.
     *
     * @param  null  $_
     * @param  array  $args
     * @return PlaylistTrack
     */
    public function store($_, array $args): PlaylistTrack
    {
        /** @var Playlist $playlist */
        $playlist = Arr::pull($args, 'playlist');

        $action = new StoreTrackAction();

        /** @var PlaylistTrack $stored */
        $stored = $action->store($playlist, PlaylistTrack::query(), $args);

        return $stored;
    }

    /**
     * Update the specified resource.
     *
     * @param  null  $_
     * @param  array  $args
     * @return PlaylistTrack
     */
    public function update($_, array $args): PlaylistTrack
    {
        /** @var PlaylistTrack $track */
        $track = Arr::pull($args, self::ROUTE_SLUG);

        $action = new UpdateTrackAction();

        /** @var PlaylistTrack $updated */
        $updated = $action->update($track->playlist, $track, $args);

        return $updated;
    }

    /**
     * Remove the specified resource.
     *
     * @param  null  $_
     * @param  array  $args
     * @return PlaylistTrack
     */
    public function destroy($_, array $args): PlaylistTrack
    {
        /** @var PlaylistTrack $track */
        $track = Arr::get($args, self::ROUTE_SLUG);

        $action = new DestroyTrackAction();

        /** @var PlaylistTrack $destroyed */
        $destroyed = $action->destroy($track->playlist, $track);

        return $destroyed;
    }

    /**
     * Restore the specified resource.
     *
     * @param  null  $_
     * @param  array  $args
     * @return PlaylistTrack
     */
    public function restore($_, array $args): PlaylistTrack
    {
        /** @var PlaylistTrack $track */
        $track = Arr::get($args, self::ROUTE_SLUG);

        $action = new RestoreTrackAction();

        /** @var PlaylistTrack $restored */
        $restored = $action->restore($track->playlist, $track);

        return $restored;
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  null  $_
     * @param  array  $args
     * @return JsonResponse
     */
    public function forceDelete($_, array $args): JsonResponse
    {
        /** @var PlaylistTrack $track */
        $track = Arr::get($args, self::ROUTE_SLUG);

        $action = new ForceDeleteTrackAction();

        $message = $action->forceDelete($track->playlist, $track);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
