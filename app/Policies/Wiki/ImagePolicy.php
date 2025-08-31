<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class ImagePolicy extends BasePolicy
{
    public function attachAnyArtist(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Image::class)) && $user->can(CrudPermission::CREATE->format(Artist::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyArtist(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Image::class)) && $user->can(CrudPermission::DELETE->format(Artist::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyAnime(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Image::class)) && $user->can(CrudPermission::CREATE->format(Anime::class))
        ? Response::allow()
            : Response::deny();
    }

    public function detachAnyAnime(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Image::class)) && $user->can(CrudPermission::DELETE->format(Anime::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyStudio(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Image::class)) && $user->can(CrudPermission::CREATE->format(Studio::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyStudio(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Image::class)) && $user->can(CrudPermission::DELETE->format(Studio::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyPlaylist(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function attachPlaylist(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyPlaylist(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
