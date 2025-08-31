<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class ExternalResourcePolicy extends BasePolicy
{
    public function attachAnyAnime(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Anime::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyAnime(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Anime::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyArtist(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Artist::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyArtist(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Artist::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnySong(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Song::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnySong(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Song::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyStudio(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Studio::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyStudio(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Studio::class))
            ? Response::allow()
            : Response::deny();
    }
}
