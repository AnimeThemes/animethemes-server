<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use App\Policies\BasePolicy;

/**
 * Class ArtistPolicy.
 */
class ArtistPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any resource to the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach a resource to the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach a resource from the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach any song to the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnySong(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach a song to the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachSong(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach a song from the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachSong(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach any group/member to the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach a group/member to the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach a group/member from the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach any image to the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach an image to the artist.
     *
     * @param  User  $user
     * @param  Artist  $artist
     * @param  Image  $image
     * @return bool
     */
    public function attachImage(User $user, Artist $artist, Image $image): bool
    {
        $attached = ArtistImage::query()
            ->where($artist->getKeyName(), $artist->getKey())
            ->where($image->getKeyName(), $image->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach an image from the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachImage(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }
}
