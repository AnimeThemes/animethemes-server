<?php

declare(strict_types=1);

namespace App\Policies\List\Playlist;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class PlaylistTrackPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        if ($user !== null) {
            return ($playlist?->user()->is($user) || $playlist?->visibility !== PlaylistVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class));
        }

        return $playlist?->visibility !== PlaylistVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  PlaylistTrack  $track
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function view(?User $user, Model $track): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        if ($user !== null) {
            return ($playlist?->user()->is($user) || $playlist?->visibility !== PlaylistVisibility::PRIVATE) && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class));
        }

        return $playlist?->visibility !== PlaylistVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $playlist?->user()->is($user) && parent::create($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  PlaylistTrack  $track
     */
    public function update(User $user, Model $track): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $playlist?->user()->is($user) && parent::update($user, $track);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  PlaylistTrack  $track
     */
    public function delete(User $user, Model $track): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value);
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $playlist?->user()->is($user) && parent::delete($user, $track);
    }
}
