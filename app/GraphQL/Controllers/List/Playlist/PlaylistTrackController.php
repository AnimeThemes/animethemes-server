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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * @extends BaseController<PlaylistTrack>
 */
class PlaylistTrackController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<PlaylistTrack>  $builder
     * @param  array<string, mixed>  $args
     * @return Builder<PlaylistTrack>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, 'playlist');

        $builder->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey());

        return $builder;
    }

    /**
     * Apply the query builder to the show query.
     *
     * @param  Builder<PlaylistTrack>  $builder
     * @param  array<string, mixed>  $args
     * @return Builder<PlaylistTrack>
     */
    public function show(Builder $builder, mixed $value, mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        $builder->whereRelation(PlaylistTrack::RELATION_PLAYLIST, Playlist::ATTRIBUTE_HASHID, Arr::get($args, 'playlist'));
        $builder->whereKey(Arr::get($args, self::ROUTE_SLUG));

        return $builder;
    }

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
