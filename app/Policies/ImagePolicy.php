<?php

namespace App\Policies;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagePolicy
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
     * @param  \App\Models\Image  $image
     * @return mixed
     */
    public function view(User $user, Image $image)
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
     * @param  \App\Models\Image  $image
     * @return mixed
     */
    public function update(User $user, Image $image)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Image  $image
     * @return mixed
     */
    public function delete(User $user, Image $image)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Image  $image
     * @return mixed
     */
    public function restore(User $user, Image $image)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Image  $image
     * @return mixed
     */
    public function forceDelete(User $user, Image $image)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach any artist to the image.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Image  $image
     * @return mixed
     */
    public function attachAnyArtist(User $user, Image $image)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach an artist to the image.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Image  $image
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function attachArtist(User $user, Image $image, Artist $artist)
    {
        if ($image->artists->contains($artist)) {
            return false;
        }

        return $this->attachAnyArtist($user, $image);
    }

    /**
     * Determine whether the user can detach an artist from the image.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Image  $image
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function detachArtist(User $user, Image $image, Artist $artist)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach any anime to the image.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Image  $image
     * @return mixed
     */
    public function attachAnyAnime(User $user, Image $image)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach an anime to the image.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Image  $image
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function attachAnime(User $user, Image $image, Anime $anime)
    {
        if ($image->anime->contains($anime)) {
            return false;
        }

        return $this->attachAnyAnime($user, $image);
    }

    /**
     * Determine whether the user can detach an anime from the image.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Image  $image
     * @param  \App\Models\Anime  $anime
     * @return mixed
     */
    public function detachAnime(User $user, Image $image, Anime $anime)
    {
        return $user->isContributor() || $user->isAdmin();
    }
}
