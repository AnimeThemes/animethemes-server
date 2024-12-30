<?php

declare(strict_types=1);

namespace App\Policies\List\Playlist;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrackPolicy.
 */
class PlaylistTrackPolicy extends BasePolicy
{
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
            ? ($playlist?->user()->is($user) || PlaylistVisibility::PRIVATE !== $playlist?->visibility) && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class))
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
    public function view(?User $user, BaseModel|Model $track): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $user !== null
            ? ($playlist?->user()->is($user) || PlaylistVisibility::PRIVATE !== $playlist?->visibility) && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class))
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
            return $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $playlist?->user()->is($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  PlaylistTrack  $track
     * @return bool
     */
    public function update(User $user, BaseModel|Model $track): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return !$track->trashed() && $playlist?->user()->is($user) && $user->can(CrudPermission::UPDATE->format(PlaylistTrack::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  PlaylistTrack  $track
     * @return bool
     */
    public function delete(User $user, BaseModel|Model $track): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return !$track->trashed() && $playlist?->user()->is($user) && $user->can(CrudPermission::DELETE->format(PlaylistTrack::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  PlaylistTrack  $track
     * @return bool
     */
    public function restore(User $user, BaseModel|Model $track): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $track->trashed() && $playlist?->user()->is($user) && $user->can(ExtendedCrudPermission::RESTORE->format(PlaylistTrack::class));
    }
}
