<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Policies\BasePolicy;

class ArtistPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any song to the artist.
     */
    public function attachAnySong(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Artist::class)) && $user->can(CrudPermission::CREATE->format(Song::class));
    }

    /**
     * Determine whether the user can detach any song from the artist.
     */
    public function detachAnySong(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Artist::class)) && $user->can(CrudPermission::DELETE->format(Song::class));
    }

    /**
     * Determine whether the user can attach any resource to the artist.
     */
    public function attachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Artist::class)) && $user->can(CrudPermission::CREATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can detach a resource from the artist.
     */
    public function detachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Artist::class)) && $user->can(CrudPermission::DELETE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can attach any group/member to the artist.
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach any group/member from the artist.
     */
    public function detachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach any image to the artist.
     */
    public function attachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Artist::class)) && $user->can(CrudPermission::CREATE->format(Image::class));
    }

    /**
     * Determine whether the user can detach any image from the artist.
     */
    public function detachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Artist::class)) && $user->can(CrudPermission::DELETE->format(Image::class));
    }
}
