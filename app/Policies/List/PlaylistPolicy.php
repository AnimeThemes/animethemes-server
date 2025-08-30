<?php

declare(strict_types=1);

namespace App\Policies\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class PlaylistPolicy extends BasePolicy
{
    public function viewAny(?User $user): Response
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return $user === null || $user->can(CrudPermission::VIEW->format(Playlist::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Playlist  $playlist
     */
    public function view(?User $user, Model $playlist): Response
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(RoleEnum::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        if ($user !== null) {
            return ($playlist->user()->is($user) || $playlist->visibility !== PlaylistVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(Playlist::class))
                ? Response::allow()
                : Response::deny();
        }

        return $playlist->visibility !== PlaylistVisibility::PRIVATE
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

        return parent::create($user);
    }

    /**
     * @param  Playlist  $playlist
     */
    public function update(User $user, Model $playlist): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return parent::update($user, $playlist)->allowed() && $playlist->user()->is($user)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Playlist  $playlist
     */
    public function delete(User $user, Model $playlist): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return parent::delete($user, $playlist)->allowed() && $playlist->user()->is($user)
            ? Response::allow()
            : Response::deny();
    }

    public function addPlaylistTrack(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyImage(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function attachImage(User $user, Playlist $playlist, Image $image): Response
    {
        if ($playlist->user()->isNot($user)) {
            return Response::deny();
        }

        return $user->can(CrudPermission::CREATE->format(Playlist::class))
            && $user->can(CrudPermission::CREATE->format(Image::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyImage(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachImage(User $user, Playlist $playlist): Response
    {
        return $playlist->user()->is($user)
            && $user->can(CrudPermission::DELETE->format(Playlist::class))
            && $user->can(CrudPermission::DELETE->format(Image::class))
            ? Response::allow()
            : Response::deny();
    }
}
