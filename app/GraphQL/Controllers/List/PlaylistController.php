<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\List;

use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\CreatePlaylistMutation;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\UpdatePlaylistMutation;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @extends BaseController<Playlist>
 */
class PlaylistController extends BaseController
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function store($root, array $args): Playlist
    {
        $validated = $this->validated($args, CreatePlaylistMutation::class);

        $parameters = [
            ...$validated,
            Playlist::ATTRIBUTE_USER => Auth::id(),
        ];

        return $this->storeAction->store(Playlist::query(), $parameters);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function update($root, array $args): Playlist
    {
        /** @var Playlist $playlist */
        $playlist = Arr::pull($args, self::MODEL);

        $validated = $this->validated($args, UpdatePlaylistMutation::class);

        return $this->updateAction->update($playlist, $validated);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function destroy($root, array $args): array
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, self::MODEL);

        $message = $this->destroyAction->forceDelete($playlist);

        return [
            'message' => $message,
        ];
    }
}
