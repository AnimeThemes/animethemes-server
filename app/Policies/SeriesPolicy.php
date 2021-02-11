<?php

namespace App\Policies;

use App\Models\Anime;
use App\Models\Series;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeriesPolicy
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
     * @param  \App\Models\Series  $series
     * @return mixed
     */
    public function view(User $user, Series $series)
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
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Series  $series
     * @return mixed
     */
    public function update(User $user, Series $series)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Series  $series
     * @return mixed
     */
    public function delete(User $user, Series $series)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Series  $series
     * @return mixed
     */
    public function restore(User $user, Series $series)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Series  $series
     * @return mixed
     */
    public function forceDelete(User $user, Series $series)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach any anime to the series.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Series  $series
     * @return mixed
     */
    public function attachAnyAnime(User $user, Series $series)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach an anime to the series.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Series  $series
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function attachAnime(User $user, Series $series, Anime $anime)
    {
        if ($series->anime->contains($anime)) {
            return false;
        }

        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can detach an anime from the series.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Series  $series
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function detachAnime(User $user, Series $series, Anime $anime)
    {
        return $user->isContributor() || $user->isAdmin();
    }
}
