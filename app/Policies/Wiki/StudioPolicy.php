<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class StudioPolicy extends BasePolicy
{
    public function attachAnyAnime(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Studio::class)) && $user->can(CrudPermission::CREATE->format(Anime::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyAnime(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Studio::class)) && $user->can(CrudPermission::DELETE->format(Anime::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyExternalResource(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Studio::class)) && $user->can(CrudPermission::CREATE->format(ExternalResource::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyExternalResource(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Studio::class)) && $user->can(CrudPermission::DELETE->format(ExternalResource::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyImage(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Studio::class)) && $user->can(CrudPermission::CREATE->format(Image::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyImage(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Studio::class)) && $user->can(CrudPermission::DELETE->format(Image::class))
            ? Response::allow()
            : Response::deny();
    }
}
