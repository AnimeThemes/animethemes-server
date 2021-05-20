<?php

namespace App\Policies;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\Image;
use App\Models\User;
use App\Pivots\AnimeImage;
use App\Pivots\ArtistImage;
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
        return $user->hasCurrentTeamPermission('image:create');
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
        return $user->hasCurrentTeamPermission('image:update');
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
        return $user->hasCurrentTeamPermission('image:delete');
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
        return $user->hasCurrentTeamPermission('image:restore');
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
        return $user->hasCurrentTeamPermission('image:forceDelete');
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
        return $user->hasCurrentTeamPermission('image:update');
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
        if (ArtistImage::where($artist->getKeyName(), $artist->getKey())->where($image->getKeyName(), $image->getKey())->exists()) {
            return false;
        }

        return $user->hasCurrentTeamPermission('image:update');
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
        return $user->hasCurrentTeamPermission('image:update');
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
        return $user->hasCurrentTeamPermission('image:update');
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
        if (AnimeImage::where($anime->getKeyName(), $anime->getKey())->where($image->getKeyName(), $image->getKey())->exists()) {
            return false;
        }

        return $user->hasCurrentTeamPermission('image:update');
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
        return $user->hasCurrentTeamPermission('image:update');
    }
}
