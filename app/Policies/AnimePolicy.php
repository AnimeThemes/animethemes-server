<?php

namespace App\Policies;

use App\Models\Anime;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Series;
use App\Models\User;
use App\Pivots\AnimeImage;
use App\Pivots\AnimeSeries;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnimePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function view(User $user, Anime $anime)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasCurrentTeamPermission('anime:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function update(User $user, Anime $anime)
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function delete(User $user, Anime $anime)
    {
        return $user->hasCurrentTeamPermission('anime:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function restore(User $user, Anime $anime)
    {
        return $user->hasCurrentTeamPermission('anime:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function forceDelete(User $user, Anime $anime)
    {
        return $user->hasCurrentTeamPermission('anime:forceDelete');
    }

    /**
     * Determine whether the user can attach any series to the anime.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function attachAnySeries(User $user, Anime $anime)
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach a series to the anime.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @param  \App\Models\Series  $series
     * @return mixed
     */
    public function attachSeries(User $user, Anime $anime, Series $series)
    {
        if (AnimeSeries::where($anime->getKeyName(), $anime->getKey())->where($series->getKeyName(), $series->getKey())->exists()) {
            return false;
        }

        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can detach a series from the anime.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @param  \App\Models\Series  $series
     * @return mixed
     */
    public function detachSeries(User $user, Anime $anime, Series $series)
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach any resource to the anime.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function attachAnyExternalResource(User $user, Anime $anime)
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach a resource to the anime.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function attachExternalResource(User $user, Anime $anime, ExternalResource $externalResource)
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can detach a resource from the anime.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function detachExternalResource(User $user, Anime $anime, ExternalResource $externalResource)
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach any image to the anime.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function attachAnyImage(User $user, Anime $anime)
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can attach an image to the anime.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @param  \App\Models\Image  $image
     * @return mixed
     */
    public function attachImage(User $user, Anime $anime, Image $image)
    {
        if (AnimeImage::where($anime->getKeyName(), $anime->getKey())->where($image->getKeyName(), $image->getKey())->exists()) {
            return false;
        }

        return $user->hasCurrentTeamPermission('anime:update');
    }

    /**
     * Determine whether the user can detach an image from the anime.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Anime  $anime
     * @param  \App\Models\Image  $image
     * @return mixed
     */
    public function detachImage(User $user, Anime $anime, Image $image)
    {
        return $user->hasCurrentTeamPermission('anime:update');
    }
}
