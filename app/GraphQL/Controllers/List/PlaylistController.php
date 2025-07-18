<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\List;

use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Definition\Mutations\Rest\List\Playlist\CreatePlaylistMutation;
use App\GraphQL\Definition\Mutations\Rest\List\Playlist\UpdatePlaylistMutation;
use App\Models\List\Playlist;
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
     * @param  null  $_
     * @param  array  $args
     * @return Playlist
     */
    public function store($_, array $args): Playlist
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
     * @param  null  $_
     * @param  array  $args
     * @return Playlist
     */
    public function update($_, array $args): Playlist
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
     * @param  null  $_
     * @param  array  $args
     * @return string
     */
    public function destroy($_, array $args): string
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, self::ROUTE_SLUG);

        $message = $this->destroyAction->forceDelete($playlist);

        return $message;
    }
}
