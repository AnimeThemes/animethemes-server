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

class StudioPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any anime to the studio.
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Studio::class)) && $user->can(CrudPermission::CREATE->format(Anime::class));
    }

    /**
     * Determine whether the user can detach any anime from the studio.
     */
    public function detachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Studio::class)) && $user->can(CrudPermission::DELETE->format(Anime::class));
    }

    /**
     * Determine whether the user can attach any resource to the studio.
     */
    public function attachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Studio::class)) && $user->can(CrudPermission::CREATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can detach any resource from the studio.
     */
    public function detachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Studio::class)) && $user->can(CrudPermission::DELETE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can attach any image to the studio.
     */
    public function attachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Studio::class)) && $user->can(CrudPermission::CREATE->format(Image::class));
    }

    /**
     * Determine whether the user can detach any image from the studio.
     */
    public function detachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Studio::class)) && $user->can(CrudPermission::DELETE->format(Image::class));
    }
}
