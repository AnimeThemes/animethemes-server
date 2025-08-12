<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\GraphQL\Controllers\BaseController;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\CreatePlaylistMutation;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\UpdatePlaylistMutation;
use App\Models\List\Playlist;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @extends BaseController<Playlist>
 */
class PlaylistController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Apply the query builder to the index query.
     *
     * @param  Builder<Playlist>  $builder
     * @param  array  $args
     * @return Builder<Playlist>
     */
    public function index(Builder $builder, mixed $value, mixed $root, array $args, $context, ResolveInfo $resolveInfo): Builder
    {
        $builder->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC->value);

        if ($user = Auth::user()) {
            return $builder->orWhereBelongsTo($user, Playlist::RELATION_USER);
        }

        return $builder;
    }

    /**
     * Apply the query builder to the show query.
     *
     * @param  Builder<Playlist>  $builder
     * @param  array  $args
     * @return Builder<Playlist>
     */
    public function show(Builder $builder, mixed $value, mixed $root, array $args, $context, ResolveInfo $resolveInfo): Builder
    {
        return $builder
            ->whereKey(Arr::get($args, self::ROUTE_SLUG)->getKey());
    }

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
