<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Studio;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class AnimePolicy extends BasePolicy
{
    public function addAnyAnimeSynonym(User $user): Response
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeSynonym::class))
            ? Response::allow()
            : Response::deny();
    }

    public function addAnyAnimeTheme(User $user): Response
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeTheme::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnySeries(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Anime::class)) && $user->can(CrudPermission::CREATE->format(Series::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnySeries(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Anime::class)) && $user->can(CrudPermission::DELETE->format(Series::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyExternalResource(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Anime::class)) && $user->can(CrudPermission::CREATE->format(ExternalResource::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyExternalResource(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Anime::class)) && $user->can(CrudPermission::DELETE->format(ExternalResource::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyImage(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Anime::class)) && $user->can(CrudPermission::CREATE->format(Image::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyImage(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Anime::class)) && $user->can(CrudPermission::DELETE->format(Image::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyStudio(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Anime::class)) && $user->can(CrudPermission::CREATE->format(Studio::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyStudio(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Anime::class)) && $user->can(CrudPermission::DELETE->format(Studio::class))
            ? Response::allow()
            : Response::deny();
    }

    public function addEntry(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
