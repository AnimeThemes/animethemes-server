<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\List;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * Class PlaylistMutator.
 */
class PlaylistMutator
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
        $parameters = [
            ...$args,
            Playlist::ATTRIBUTE_USER => Auth::id(),
        ];

        $action = new StoreAction();

        /** @var Playlist $stored */
        $stored = $action->store(Playlist::query(), $parameters);

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

        $action = new UpdateAction();

        /** @var Playlist $updated */
        $updated = $action->update($playlist, $args);

        return $updated;
    }

    /**
     * Remove the specified resource.
     *
     * @param  null  $_
     * @param  array  $args
     * @return Playlist
     */
    public function destroy($_, array $args): Playlist
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, self::ROUTE_SLUG);

        $action = new DestroyAction();

        /** @var Playlist $destroyed */
        $destroyed = $action->destroy($playlist);

        return $destroyed;
    }
}
