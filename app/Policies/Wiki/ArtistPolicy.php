<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistImage;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\ArtistSong;
use App\Policies\BasePolicy;

/**
 * Class ArtistPolicy.
 */
class ArtistPolicy extends BasePolicy
{
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
     * Determine whether the user can attach any song to the artist.
     *
     * @param  User  $user
     * @param  Artist  $artist
     * @param  Song  $song
     * @return bool
     */
    public function attachSong(User $user, Artist $artist, Song $song): bool
    {
        $attached = ArtistSong::query()
            ->where(ArtistSong::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistSong::ATTRIBUTE_SONG, $song->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach any song from the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnySong(User $user): bool
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
     * @param  Artist  $artist
     * @param  ExternalResource  $resource
     * @return bool
     */
    public function attachExternalResource(User $user, Artist $artist, ExternalResource $resource): bool
    {
        $attached = ArtistResource::query()
            ->where(ArtistResource::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach a resource from the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach any resource from the artist.
     *
     * @param  User  $user
     * @param  Artist  $artist
     * @param  ExternalResource  $resource
     * @return bool
     */
    public function detachExternalResource(User $user, Artist $artist, ExternalResource $resource): bool
    {
        $attached = ArtistResource::query()
            ->where(ArtistResource::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->exists();

        return $attached && $user->can(CrudPermission::UPDATE->format(Artist::class));
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
     * @param  Artist  $artist
     * @param  Artist  $artist2
     * @return bool
     */
    public function attachArtist(User $user, Artist $artist, Artist $artist2): bool
    {
        if ($artist->is($artist2)) {
            // An artist cannot be a member/group of themselves
            return false;
        }

        $attached = ArtistMember::query()
            ->where(ArtistMember::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistMember::ATTRIBUTE_MEMBER, $artist2->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach any group/member from the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach a group/member from the artist.
     *
     * @param  User  $user
     * @param  Artist  $artist
     * @param  Artist  $artist2
     * @return bool
     */
    public function detachArtist(User $user, Artist $artist, Artist $artist2): bool
    {
        $attached = ArtistMember::query()
            ->where(ArtistMember::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistMember::ATTRIBUTE_MEMBER, $artist2->getKey())
            ->exists();

        return $attached && $user->can(CrudPermission::UPDATE->format(Artist::class));
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
            ->where(ArtistImage::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach any image from the artist.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach an image from the artist.
     *
     * @param  User  $user
     * @param  Artist  $artist
     * @param  Image  $image
     * @return bool
     */
    public function detachImage(User $user, Artist $artist, Image $image): bool
    {
        $attached = ArtistImage::query()
            ->where(ArtistImage::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->exists();

        return $attached && $user->can(CrudPermission::UPDATE->format(Artist::class));
    }
}
