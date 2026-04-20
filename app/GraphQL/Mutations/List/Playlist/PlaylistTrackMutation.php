<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\List\Playlist;

use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\StoreTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\UpdateTrackAction;
use App\Concerns\Http\RunMiddlewares;
use App\Features\AllowPlaylistManagement;
use App\Http\Middleware\Models\List\PlaylistExceedsTrackLimit;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Arr;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class PlaylistTrackMutation
{
    use RunMiddlewares;

    public static Playlist $playlist;

    /**
     * @param  array<string, mixed>  $args
     */
    public function create(null $_, array $args): PlaylistTrack
    {
        static::$playlist = Arr::pull($args, 'playlist');

        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
            PlaylistExceedsTrackLimit::class,
        ]);

        $validated = [
            PlaylistTrack::ATTRIBUTE_ENTRY => Arr::integer($args, 'entryId'),
            PlaylistTrack::ATTRIBUTE_VIDEO => Arr::integer($args, 'videoId'),
        ];

        return new StoreTrackAction()->store(static::$playlist, PlaylistTrack::query(), $validated);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function update(null $_, array $args): PlaylistTrack
    {
        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
        ]);

        /** @var PlaylistTrack $track */
        $track = Arr::get($args, 'id');

        /** @var Playlist $playlist */
        $playlist = Arr::pull($args, 'playlist');

        $validated = [
            PlaylistTrack::ATTRIBUTE_ENTRY => Arr::integer($args, 'entryId'),
            PlaylistTrack::ATTRIBUTE_VIDEO => Arr::integer($args, 'videoId'),
        ];

        return new UpdateTrackAction()->update($playlist, $track, $validated);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function delete(null $_, array $args): array
    {
        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
        ]);

        /** @var PlaylistTrack $track */
        $track = Arr::pull($args, 'id');

        /** @var Playlist $playlist */
        $playlist = Arr::pull($args, 'playlist');

        $message = new DestroyTrackAction()->destroy($playlist, $track);

        return [
            'message' => $message,
        ];
    }
}
