<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers\List;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Features\AllowPlaylistManagement;
use App\GraphQL\Resolvers\BaseResolver;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\CreatePlaylistMutation;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\UpdatePlaylistMutation;
use App\Http\Middleware\Models\List\UserExceedsPlaylistLimit;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

/**
 * @extends BaseResolver<Playlist>
 */
class PlaylistResolver extends BaseResolver
{
    public function __construct()
    {
        $this->middleware(EnsureFeaturesAreActive::using(AllowPlaylistManagement::class))->only(['store', 'update', 'destroy']);
        $this->middleware(UserExceedsPlaylistLimit::class)->only(['store']);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function store(array $args, StoreAction $action): Playlist
    {
        $this->runMiddleware();

        $validated = $this->validated($args, CreatePlaylistMutation::class);

        $parameters = [
            ...$validated,
            Playlist::ATTRIBUTE_USER => Auth::id(),
        ];

        return $action->store(Playlist::query(), $parameters);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function update(array $args, UpdateAction $action): Playlist
    {
        $this->runMiddleware();

        /** @var Playlist $playlist */
        $playlist = Arr::pull($args, self::MODEL);

        $validated = $this->validated($args, UpdatePlaylistMutation::class);

        return $action->update($playlist, $validated);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function destroy(array $args, DestroyAction $action): array
    {
        $this->runMiddleware();

        /** @var Playlist $playlist */
        $playlist = Arr::get($args, self::MODEL);

        $message = $action->forceDelete($playlist);

        return [
            'message' => $message,
        ];
    }
}
