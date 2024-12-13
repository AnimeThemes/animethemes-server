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
use App\Pivots\Wiki\AnimeImage;
use App\Pivots\Wiki\ArtistImage;
use App\Pivots\Wiki\StudioImage;
use App\Policies\BasePolicy;

/**
 * Class ImagePolicy.
 */
class ImagePolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any artist to the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can attach an artist to the image.
     *
     * @param  User  $user
     * @param  Image  $image
     * @param  Artist  $artist
     * @return bool
     */
    public function attachArtist(User $user, Image $image, Artist $artist): bool
    {
        $attached = ArtistImage::query()
            ->where(ArtistImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->where(ArtistImage::ATTRIBUTE_ARTIST, $artist->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can detach any artist from the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can detach an artist from the image.
     *
     * @param  User  $user
     * @param  Image  $image
     * @param  Artist  $artist
     * @return bool
     */
    public function detachArtist(User $user, Image $image, Artist $artist): bool
    {
        $attached = ArtistImage::query()
            ->where(ArtistImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->where(ArtistImage::ATTRIBUTE_ARTIST, $artist->getKey())
            ->exists();

        return $attached && $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can attach any anime to the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can attach an anime to the image.
     *
     * @param  User  $user
     * @param  Image  $image
     * @param  Anime  $anime
     * @return bool
     */
    public function attachAnime(User $user, Image $image, Anime $anime): bool
    {
        $attached = AnimeImage::query()
            ->where(AnimeImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->where(AnimeImage::ATTRIBUTE_ANIME, $anime->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can detach any anime from the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can detach an anime from the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnime(User $user, Image $image, Anime $anime): bool
    {
        $attached = AnimeImage::query()
            ->where(AnimeImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->where(AnimeImage::ATTRIBUTE_ANIME, $anime->getKey())
            ->exists();

        return $attached && $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can attach any studio to the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can attach a studio to the image.
     *
     * @param  User  $user
     * @param  Image  $image
     * @param  Studio  $studio
     * @return bool
     */
    public function attachStudio(User $user, Image $image, Studio $studio): bool
    {
        $attached = StudioImage::query()
            ->where(StudioImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->where(StudioImage::ATTRIBUTE_STUDIO, $studio->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can detach any studio from the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can detach a studio from the image.
     *
     * @param  User  $user
     * @param  Image  $image
     * @param  Studio  $studio
     * @return bool
     */
    public function detachStudio(User $user, Image $image, Studio $studio): bool
    {
        $attached = StudioImage::query()
            ->where(StudioImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->where(StudioImage::ATTRIBUTE_STUDIO, $studio->getKey())
            ->exists();

        return $attached && $user->can(CrudPermission::UPDATE->format(Image::class));
    }

    /**
     * Determine whether the user can attach any playlist to the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyPlaylist(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }

    /**
     * Determine whether the user can attach a playlist to the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachPlaylist(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }

    /**
     * Determine whether the user can detach any playlist from the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyPlaylist(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }

    /**
     * Determine whether the user can detach a playlist from the image.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachPlaylist(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }
}
