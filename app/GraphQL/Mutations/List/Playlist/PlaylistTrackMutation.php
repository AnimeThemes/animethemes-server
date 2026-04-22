<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\List\Playlist;

use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\StoreTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\UpdateTrackAction;
use App\Concerns\GraphQL\RunMiddlewares;
use App\Concerns\GraphQL\ValidateArgs;
use App\Features\AllowPlaylistManagement;
use App\GraphQL\Validators\List\Playlist\CreatePlaylistTrackMutationValidator;
use App\GraphQL\Validators\List\Playlist\UpdatePlaylistTrackMutationValidator;
use App\Http\Middleware\Models\List\PlaylistExceedsTrackLimit;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Arr;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PlaylistTrackMutation
{
    use RunMiddlewares;
    use ValidateArgs;

    public static Playlist $playlist;

    /**
     * @param  array<string, mixed>  $args
     */
    public function create(null $_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): PlaylistTrack
    {
        static::$playlist = Playlist::query()->firstWhere(Playlist::ATTRIBUTE_HASHID, Arr::pull($args, 'playlist'));

        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
            PlaylistExceedsTrackLimit::class,
        ]);

        $validated = $this->validated(CreatePlaylistTrackMutationValidator::class, $resolveInfo);

        $validated = [
            PlaylistTrack::ATTRIBUTE_ENTRY => Arr::integer($args, 'entryId'),
            PlaylistTrack::ATTRIBUTE_VIDEO => Arr::integer($args, 'videoId'),
        ];

        return new StoreTrackAction()->store(static::$playlist, PlaylistTrack::query(), $validated);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function update(null $_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): PlaylistTrack
    {
        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
        ]);

        $validated = $this->validated(UpdatePlaylistTrackMutationValidator::class, $resolveInfo);

        $track = PlaylistTrack::query()
            ->with(PlaylistTrack::RELATION_PLAYLIST)
            ->firstWhere(PlaylistTrack::ATTRIBUTE_HASHID, Arr::pull($args, 'id'));

        $validated = [
            PlaylistTrack::ATTRIBUTE_ENTRY => Arr::integer($args, 'entryId'),
            PlaylistTrack::ATTRIBUTE_VIDEO => Arr::integer($args, 'videoId'),
        ];

        return new UpdateTrackAction()->update($track->playlist, $track, $validated);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function delete(null $_, array $args): array
    {
        $this->runHttpMiddleware([
            EnsureFeaturesAreActive::using(AllowPlaylistManagement::class),
        ]);

        $track = PlaylistTrack::query()
            ->with(PlaylistTrack::RELATION_PLAYLIST)
            ->firstWhere(PlaylistTrack::ATTRIBUTE_HASHID, Arr::pull($args, 'id'));

        $message = new DestroyTrackAction()->destroy($track->playlist, $track);

        return [
            'message' => $message,
        ];
    }
}
