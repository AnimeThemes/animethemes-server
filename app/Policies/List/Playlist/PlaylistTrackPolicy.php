<?php

declare(strict_types=1);

namespace App\Policies\List\Playlist;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;

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
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $user !== null
            ? ($user->getKey() === $playlist?->user_id || PlaylistVisibility::PRIVATE !== $playlist?->visibility) && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class))
            : PlaylistVisibility::PRIVATE !== $playlist?->visibility;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  PlaylistTrack  $track
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function view(?User $user, PlaylistTrack $track): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $user !== null
            ? ($user->getKey() === $playlist?->user_id || PlaylistVisibility::PRIVATE !== $playlist?->visibility) && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class))
            : PlaylistVisibility::PRIVATE !== $playlist?->visibility;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $user->getKey() === $playlist?->user_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  PlaylistTrack  $track
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function update(User $user, PlaylistTrack $track): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return !$track->trashed() && $user->getKey() === $playlist?->user_id && $user->can(CrudPermission::UPDATE->format(PlaylistTrack::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  PlaylistTrack  $track
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function delete(User $user, PlaylistTrack $track): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return !$track->trashed() && $user->getKey() === $playlist?->user_id && $user->can(CrudPermission::DELETE->format(PlaylistTrack::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  PlaylistTrack  $track
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function restore(User $user, PlaylistTrack $track): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $track->trashed() && $user->getKey() === $playlist?->user_id && $user->can(ExtendedCrudPermission::RESTORE->format(PlaylistTrack::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(PlaylistTrack::class));
    }
}
