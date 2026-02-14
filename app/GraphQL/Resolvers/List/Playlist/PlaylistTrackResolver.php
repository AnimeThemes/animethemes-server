<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers\List\Playlist;

use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\StoreTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\UpdateTrackAction;
use App\GraphQL\Resolvers\BaseResolver;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\Track\CreatePlaylistTrackMutation;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\Track\UpdatePlaylistTrackMutation;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Arr;

/**
 * @extends BaseResolver<PlaylistTrack>
 */
class PlaylistTrackResolver extends BaseResolver
{
    final public const string ATTRIBUTE_ENTRY = 'entryId';
    final public const string ATTRIBUTE_VIDEO = 'videoId';

    /**
     * @param  array<string, mixed>  $args
     */
    public function store($root, array $args): PlaylistTrack
    {
        $validated = $this->validated($args, CreatePlaylistTrackMutation::class);

        $validated += [
            PlaylistTrack::ATTRIBUTE_ENTRY => Arr::integer($validated, 'entryId'),
            PlaylistTrack::ATTRIBUTE_VIDEO => Arr::integer($validated, 'videoId'),
        ];

        /** @var Playlist $playlist */
        $playlist = Arr::pull($validated, 'playlist');

        $action = new StoreTrackAction();

        return $action->store($playlist, PlaylistTrack::query(), $validated);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function update($root, array $args): PlaylistTrack
    {
        $validated = $this->validated($args, UpdatePlaylistTrackMutation::class);

        $validated += [
            PlaylistTrack::ATTRIBUTE_ENTRY => Arr::integer($validated, 'entryId'),
            PlaylistTrack::ATTRIBUTE_VIDEO => Arr::integer($validated, 'videoId'),
        ];

        /** @var PlaylistTrack $track */
        $track = Arr::pull($validated, self::MODEL);

        $action = new UpdateTrackAction();

        return $action->update($track->playlist, $track, $validated);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function destroy($root, array $args): array
    {
        /** @var PlaylistTrack $track */
        $track = Arr::get($args, self::MODEL);

        $action = new DestroyTrackAction();

        $message = $action->destroy($track->playlist, $track);

        return [
            'message' => $message,
        ];
    }
}
