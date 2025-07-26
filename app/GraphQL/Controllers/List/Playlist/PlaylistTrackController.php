<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\List\Playlist;

use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\StoreTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\UpdateTrackAction;
use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\Track\CreatePlaylistTrackMutation;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\Track\UpdatePlaylistTrackMutation;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

/**
 * Class PlaylistTrackController.
 *
 * @extends BaseController<PlaylistTrack>
 */
class PlaylistTrackController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Store a newly created resource.
     *
     * @param  null  $root
     * @param  array  $args
     */
    public function store($root, array $args): PlaylistTrack
    {
        $validated = $this->validated($args, CreatePlaylistTrackMutation::class);

        /** @var Playlist $playlist */
        $playlist = Arr::pull($validated, 'playlist');

        $action = new StoreTrackAction();

        $stored = $action->store($playlist, PlaylistTrack::query(), $validated);

        return $stored;
    }

    /**
     * Update the specified resource.
     *
     * @param  null  $root
     * @param  array  $args
     */
    public function update($root, array $args): PlaylistTrack
    {
        $validated = $this->validated($args, UpdatePlaylistTrackMutation::class);

        /** @var PlaylistTrack $track */
        $track = Arr::pull($validated, self::ROUTE_SLUG);

        $action = new UpdateTrackAction();

        $updated = $action->update($track->playlist, $track, $validated);

        return $updated;
    }

    /**
     * Remove the specified resource.
     *
     * @param  null  $root
     * @param  array  $args
     */
    public function destroy($root, array $args): JsonResponse
    {
        /** @var PlaylistTrack $track */
        $track = Arr::get($args, self::ROUTE_SLUG);

        $action = new DestroyTrackAction();

        $message = $action->destroy($track->playlist, $track);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
