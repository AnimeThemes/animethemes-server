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
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class PlaylistTrackPolicy extends BasePolicy
{
    public function viewAny(?User $user): Response
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        if ($user !== null) {
            return ($playlist?->user()->is($user) || $playlist?->visibility !== PlaylistVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class))
                ? Response::allow()
                : Response::deny();
        }

        return $playlist?->visibility !== PlaylistVisibility::PRIVATE
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  PlaylistTrack  $track
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function view(?User $user, Model $track): Response
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        if ($user !== null) {
            return ($playlist?->user()->is($user) || $playlist?->visibility !== PlaylistVisibility::PRIVATE) && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class))
                ? Response::allow()
                : Response::deny();
        }

        return $playlist?->visibility !== PlaylistVisibility::PRIVATE
            ? Response::allow()
                : Response::deny();
    }

    public function create(User $user): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $playlist?->user()->is($user) && parent::create($user)->allowed()
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  PlaylistTrack  $track
     */
    public function update(User $user, Model $track): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $playlist?->user()->is($user) && parent::update($user, $track)->allowed()
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  PlaylistTrack  $track
     */
    public function delete(User $user, Model $track): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        /** @var Playlist|null $playlist */
        $playlist = request()->route('playlist');

        return $playlist?->user()->is($user) && parent::delete($user, $track)->allowed()
            ? Response::allow()
            : Response::deny();
    }
}
