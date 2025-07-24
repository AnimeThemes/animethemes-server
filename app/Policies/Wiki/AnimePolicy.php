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
use App\Pivots\Wiki\AnimeImage;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\AnimeSeries;
use App\Pivots\Wiki\AnimeStudio;
use App\Policies\BasePolicy;

class AnimePolicy extends BasePolicy
{
    /**
     * Determine whether the user can associate any synonym to the anime.
     */
    public function addAnyAnimeSynonym(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeSynonym::class));
    }

    /**
     * Determine whether the user can associate any theme to the anime.
     */
    public function addAnyAnimeTheme(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeTheme::class));
    }

    /**
     * Determine whether the user can attach any series to the anime.
     */
    public function attachAnySeries(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Anime::class)) && $user->can(CrudPermission::CREATE->format(Series::class));
    }

    /**
     * Determine whether the user can attach a series to the anime.
     */
    public function attachSeries(User $user, Anime $anime, Series $series): bool
    {
        $attached = AnimeSeries::query()
            ->where(AnimeSeries::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeSeries::ATTRIBUTE_SERIES, $series->getKey())
            ->exists();

        return ! $attached
            && $user->can(CrudPermission::CREATE->format(Anime::class))
            && $user->can(CrudPermission::CREATE->format(Series::class));
    }

    /**
     * Determine whether the user can detach any series from the anime.
     */
    public function detachAnySeries(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Anime::class)) && $user->can(CrudPermission::DELETE->format(Series::class));
    }

    /**
     * Determine whether the user can attach any resource to the anime.
     */
    public function attachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Anime::class)) && $user->can(CrudPermission::CREATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can attach a resource to the anime.
     */
    public function attachExternalResource(User $user, Anime $anime, ExternalResource $resource): bool
    {
        $attached = AnimeResource::query()
            ->where(AnimeResource::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->exists();

        return ! $attached
            && $user->can(CrudPermission::CREATE->format(Anime::class))
            && $user->can(CrudPermission::CREATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can detach any resource from the anime.
     */
    public function detachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Anime::class)) && $user->can(CrudPermission::DELETE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can attach any image to the anime.
     */
    public function attachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Anime::class)) && $user->can(CrudPermission::CREATE->format(Image::class));
    }

    /**
     * Determine whether the user can attach an image to the anime.
     */
    public function attachImage(User $user, Anime $anime, Image $image): bool
    {
        $attached = AnimeImage::query()
            ->where(AnimeImage::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->exists();

        return ! $attached
            && $user->can(CrudPermission::CREATE->format(Anime::class))
            && $user->can(CrudPermission::CREATE->format(Image::class));
    }

    /**
     * Determine whether the user can detach any image from the anime.
     */
    public function detachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Anime::class)) && $user->can(CrudPermission::DELETE->format(Image::class));
    }

    /**
     * Determine whether the user can attach any studio to the anime.
     */
    public function attachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Anime::class)) && $user->can(CrudPermission::CREATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach a studio to the anime.
     */
    public function attachStudio(User $user, Anime $anime, Studio $studio): bool
    {
        $attached = AnimeStudio::query()
            ->where(AnimeStudio::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeStudio::ATTRIBUTE_STUDIO, $studio->getKey())
            ->exists();

        return ! $attached
            && $user->can(CrudPermission::CREATE->format(Anime::class))
            && $user->can(CrudPermission::CREATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach any studio from the anime.
     */
    public function detachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Anime::class)) && $user->can(CrudPermission::DELETE->format(Studio::class));
    }

    /**
     * Determine whether the user can add an entry to the anime.
     */
    public function addEntry(User $user): bool
    {
        return $user->hasRole(Role::ADMIN->value);
    }
}
