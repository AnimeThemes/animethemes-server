<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use App\Pivots\Wiki\StudioImage;
use App\Pivots\Wiki\StudioResource;
use App\Policies\BasePolicy;

/**
 * Class StudioPolicy.
 */
class StudioPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any anime to the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach an anime to the studio.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @param  Anime  $anime
     * @return bool
     */
    public function attachAnime(User $user, Studio $studio, Anime $anime): bool
    {
        $attached = AnimeStudio::query()
            ->where(AnimeStudio::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(AnimeStudio::ATTRIBUTE_ANIME, $anime->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach any anime from the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach an anime from the studio.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @param  Anime  $anime
     * @return bool
     */
    public function detachAnime(User $user, Studio $studio, Anime $anime): bool
    {
        $attached = AnimeStudio::query()
            ->where(AnimeStudio::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(AnimeStudio::ATTRIBUTE_ANIME, $anime->getKey())
            ->exists();

        return $attached && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach any resource to the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach a resource to the studio.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @param  ExternalResource  $resource
     * @return bool
     */
    public function attachExternalResource(User $user, Studio $studio, ExternalResource $resource): bool
    {
        $attached = StudioResource::query()
            ->where(StudioResource::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach any resource from the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach a resource from the studio.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @param  ExternalResource  $resource
     * @return bool
     */
    public function detachExternalResource(User $user, Studio $studio, ExternalResource $resource): bool
    {
        $attached = StudioResource::query()
            ->where(StudioResource::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->exists();

        return $attached && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach any image to the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach an image to the studio.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @param  Image  $image
     * @return bool
     */
    public function attachImage(User $user, Studio $studio, Image $image): bool
    {
        $attached = StudioImage::query()
            ->where(StudioImage::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach any image from the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach an image from the studio.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @param  Image  $image
     * @return bool
     */
    public function detachImage(User $user, Studio $studio, Image $image): bool
    {
        $attached = StudioImage::query()
            ->where(StudioImage::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->exists();

        return $attached && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }
}
