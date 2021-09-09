<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Studio;
use App\Pivots\AnimeImage;
use App\Pivots\AnimeSeries;
use App\Pivots\AnimeStudio;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AnimePolicy.
 */
class AnimePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return bool
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return bool
     */
    public function view(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:forceDelete');
    }

    /**
     * Determine whether the user can attach any series to the anime.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnySeries(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach a series to the anime.
     *
     * @param  User  $user
     * @param  Anime  $anime
     * @param  Series  $series
     * @return bool
     */
    public function attachSeries(User $user, Anime $anime, Series $series): bool
    {
        $attached = AnimeSeries::query()
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($series->getKeyName(), $series->getKey())
            ->exists();

        return ! $attached && $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can detach a series from the anime.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachSeries(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach any resource to the anime.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyExternalResource(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach a resource to the anime.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachExternalResource(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can detach a resource from the anime.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachExternalResource(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach any image to the anime.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyImage(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach an image to the anime.
     *
     * @param  User  $user
     * @param  Anime  $anime
     * @param  Image  $image
     * @return bool
     */
    public function attachImage(User $user, Anime $anime, Image $image): bool
    {
        $attached = AnimeImage::query()
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($image->getKeyName(), $image->getKey())
            ->exists();

        return ! $attached && $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can detach an image from the anime.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachImage(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach any studio to the anime.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyStudio(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach a studio to the anime.
     *
     * @param  User  $user
     * @param  Anime  $anime
     * @param  Studio  $studio
     * @return bool
     */
    public function attachStudio(User $user, Anime $anime, Studio $studio): bool
    {
        $attached = AnimeStudio::query()
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($studio->getKeyName(), $studio->getKey())
            ->exists();

        return ! $attached && $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can detach a studio from the anime.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachStudio(User $user): bool
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }
}
