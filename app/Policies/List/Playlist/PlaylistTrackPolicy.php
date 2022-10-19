<?php

declare(strict_types=1);

namespace App\Policies\List\Playlist;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;

/**
 * Class TrackPolicy.
 */
class PlaylistTrackPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return Nova::whenServing(
            fn (): bool => $user !== null && $user->hasRole('Admin'),
            fn (): bool => $user === null || $user->can('view playlist track')
        );
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  PlaylistTrack  $track
     * @param  Playlist  $playlist
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function view(?User $user, PlaylistTrack $track, Playlist $playlist): bool
    {
        return Nova::whenServing(
            fn (): bool => $user !== null && $user->hasRole('Admin'),
            fn (): bool => $user !== null
                ? $user->can('view playlist track') && ($user->getKey() === $playlist->user_id || PlaylistVisibility::PRIVATE()->isNot($playlist->visibility))
                : PlaylistVisibility::PRIVATE()->isNot($playlist->visibility)
        );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return Nova::whenServing(
            fn (): bool => $user->hasRole('Admin'),
            function (Request $request) use ($user): bool {
                /** @var Playlist|null $playlist */
                $playlist = $request->route('playlist');

                return $user->getKey() === $playlist->user_id;
            }
        );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  PlaylistTrack  $track
     * @param  Playlist  $playlist
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function update(User $user, PlaylistTrack $track, Playlist $playlist): bool
    {
        return Nova::whenServing(
            fn (): bool => $user->hasRole('Admin'),
            fn (): bool => $user->can('update playlist track') && $user->getKey() === $playlist->user_id
        );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  PlaylistTrack  $track
     * @param  Playlist  $playlist
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function delete(User $user, PlaylistTrack $track, Playlist $playlist): bool
    {
        return Nova::whenServing(
            fn (): bool => $user->hasRole('Admin'),
            fn (): bool => $user->can('delete playlist track') && $user->getKey() === $playlist->user_id
        );
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  PlaylistTrack  $track
     * @param  Playlist  $playlist
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function restore(User $user, PlaylistTrack $track, Playlist $playlist): bool
    {
        return Nova::whenServing(
            fn (): bool => $user->hasRole('Admin'),
            fn (): bool => $user->can('restore playlist track') && $user->getKey() === $playlist->user_id
        );
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can('force delete playlist track');
    }
}
