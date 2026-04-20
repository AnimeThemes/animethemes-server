<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\List;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Concerns\Http\RunMiddlewares;
use App\Features\AllowPlaylistManagement;
use App\Http\Middleware\Models\List\UserExceedsPlaylistLimit;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class PlaylistMutation
{
    use RunMiddlewares;

    /**
     * @param  array<string, mixed>  $args
     */
    public function create(null $_, array $args): Playlist
    {
        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
            UserExceedsPlaylistLimit::class,
        ]);

        $parameters = [
            ...$args,
            Playlist::ATTRIBUTE_USER => Auth::id(),
        ];

        return new StoreAction()->store(Playlist::query(), $parameters);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function update(null $_, array $args): Playlist
    {
        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
            UserExceedsPlaylistLimit::class,
        ]);

        /** @var Playlist $playlist */
        $playlist = Arr::pull($args, 'id');

        return new UpdateAction()->update($playlist, $args);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function delete(null $_, array $args): array
    {
        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
        ]);

        /** @var Playlist $playlist */
        $playlist = Arr::pull($args, 'id');

        $message = new DestroyAction()->forceDelete($playlist);

        return [
            'message' => $message,
        ];
    }
}
