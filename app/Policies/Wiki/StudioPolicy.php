<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use App\Pivots\Wiki\StudioImage;
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
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($studio->getKeyName(), $studio->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach an anime from the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
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
     * @return bool
     */
    public function attachExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach a resource from the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
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
            ->where($studio->getKeyName(), $studio->getKey())
            ->where($image->getKeyName(), $image->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach an image from the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachImage(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }
}
