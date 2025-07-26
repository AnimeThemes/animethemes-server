<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\List;

use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\CreatePlaylistMutation;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\UpdatePlaylistMutation;
use App\Models\List\Playlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * Class PlaylistController.
 *
 * @extends BaseController<Playlist>
 */
class PlaylistController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Store a newly created resource.
     *
     * @param  null  $root
     * @param  array  $args
     */
    public function store($root, array $args): Playlist
    {
        $validated = $this->validated($args, CreatePlaylistMutation::class);

        $parameters = [
            ...$validated,
            Playlist::ATTRIBUTE_USER => Auth::id(),
        ];

        $stored = $this->storeAction->store(Playlist::query(), $parameters);

        return $stored;
    }

    /**
     * Update the specified resource.
     *
     * @param  null  $root
     * @param  array  $args
     */
    public function update($root, array $args): Playlist
    {
        /** @var Playlist $playlist */
        $playlist = Arr::pull($args, self::ROUTE_SLUG);

        $validated = $this->validated($args, UpdatePlaylistMutation::class);

        $updated = $this->updateAction->update($playlist, $validated);

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
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, self::ROUTE_SLUG);

        $message = $this->destroyAction->forceDelete($playlist);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
