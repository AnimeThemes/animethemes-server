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

class ExternalResourcePolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any anime to the resource.
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Anime::class));
    }

    /**
     * Determine whether the user can detach any anime from the resource.
     */
    public function detachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Anime::class));
    }

    /**
     * Determine whether the user can attach any artist to the resource.
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach any artist from the resource.
     */
    public function detachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach any song to the resource.
     */
    public function attachAnySong(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Song::class));
    }

    /**
     * Determine whether the user can detach any song from the resource.
     */
    public function detachAnySong(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Song::class));
    }

    /**
     * Determine whether the user can attach any studio to the resource.
     */
    public function attachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach any studio from the resource.
     */
    public function detachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Studio::class));
    }
}
