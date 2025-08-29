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

class ImagePolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any artist to the image.
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Image::class)) && $user->can(CrudPermission::CREATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach any artist from the image.
     */
    public function detachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Image::class)) && $user->can(CrudPermission::DELETE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach any anime to the image.
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Image::class)) && $user->can(CrudPermission::CREATE->format(Anime::class));
    }

    /**
     * Determine whether the user can detach any anime from the image.
     */
    public function detachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Image::class)) && $user->can(CrudPermission::DELETE->format(Anime::class));
    }

    /**
     * Determine whether the user can attach any studio to the image.
     */
    public function attachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Image::class)) && $user->can(CrudPermission::CREATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach any studio from the image.
     */
    public function detachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Image::class)) && $user->can(CrudPermission::DELETE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach any playlist to the image.
     */
    public function attachAnyPlaylist(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }

    /**
     * Determine whether the user can attach a playlist to the image.
     */
    public function attachPlaylist(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }

    /**
     * Determine whether the user can detach any playlist from the image.
     */
    public function detachAnyPlaylist(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }
}
