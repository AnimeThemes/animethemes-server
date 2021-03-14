<?php

namespace App\Policies;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExternalResourcePolicy
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
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function view(User $user, ExternalResource $externalResource)
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
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function update(User $user, ExternalResource $externalResource)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function delete(User $user, ExternalResource $externalResource)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function restore(User $user, ExternalResource $externalResource)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function forceDelete(User $user, ExternalResource $externalResource)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can attach any artist to the resource.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function attachAnyArtist(User $user, ExternalResource $externalResource)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach an artist to the resource.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ExternalResource  $externalResource
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function attachArtist(User $user, ExternalResource $externalResource, Artist $artist)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can detach an artist from the resource.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ExternalResource  $externalResource
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function detachArtist(User $user, ExternalResource $externalResource, Artist $artist)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach any anime to the resource.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function attachAnyAnime(User $user, ExternalResource $externalResource)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach an anime to the resource.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ExternalResource  $externalResource
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function attachAnime(User $user, ExternalResource $externalResource, Anime $anime)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can detach an anime from the resource.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ExternalResource  $externalResource
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function detachAnime(User $user, ExternalResource $externalResource, Anime $anime)
    {
        return $user->isContributor() || $user->isAdmin();
    }
}
