<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Auth\User;
use App\Pivots\AnimeImage;
use App\Pivots\ArtistImage;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ImagePolicy.
 */
class ImagePolicy
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
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasCurrentTeamPermission('image:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasCurrentTeamPermission('image:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('image:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->hasCurrentTeamPermission('image:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('image:forceDelete');
    }

    /**
     * Determine whether the user can attach any artist to the image.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('image:update');
    }

    /**
     * Determine whether the user can attach an artist to the image.
     *
     * @param User $user
     * @param Image $image
     * @param Artist $artist
     * @return bool
     */
    public function attachArtist(User $user, Image $image, Artist $artist): bool
    {
        if (ArtistImage::where($artist->getKeyName(), $artist->getKey())->where($image->getKeyName(), $image->getKey())->exists()) {
            return false;
        }

        return $user->hasCurrentTeamPermission('image:update');
    }

    /**
     * Determine whether the user can detach an artist from the image.
     *
     * @param User $user
     * @return bool
     */
    public function detachArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('image:update');
    }

    /**
     * Determine whether the user can attach any anime to the image.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->hasCurrentTeamPermission('image:update');
    }

    /**
     * Determine whether the user can attach an anime to the image.
     *
     * @param User $user
     * @param Image $image
     * @param Anime $anime
     * @return bool
     */
    public function attachAnime(User $user, Image $image, Anime $anime): bool
    {
        if (AnimeImage::where($anime->getKeyName(), $anime->getKey())->where($image->getKeyName(), $image->getKey())->exists()) {
            return false;
        }

        return $user->hasCurrentTeamPermission('image:update');
    }

    /**
     * Determine whether the user can detach an anime from the image.
     *
     * @param User $user
     * @return bool
     */
    public function detachAnime(User $user): bool
    {
        return $user->hasCurrentTeamPermission('image:update');
    }
}
