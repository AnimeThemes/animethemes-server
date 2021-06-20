<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Auth\User;
use App\Pivots\ArtistImage;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ArtistPolicy.
 */
class ArtistPolicy
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
        return $user->hasCurrentTeamPermission('artist:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:forceDelete');
    }

    /**
     * Determine whether the user can attach any resource to the artist.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyExternalResource(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can attach a resource to the artist.
     *
     * @param User $user
     * @return bool
     */
    public function attachExternalResource(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can detach a resource from the artist.
     *
     * @param User $user
     * @return bool
     */
    public function detachExternalResource(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can attach any song to the artist.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnySong(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can attach a song to the artist.
     *
     * @param User $user
     * @return bool
     */
    public function attachSong(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can detach a song from the artist.
     *
     * @param User $user
     * @return bool
     */
    public function detachSong(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can attach any group/member to the artist.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can attach a group/member to the artist.
     *
     * @param User $user
     * @return bool
     */
    public function attachArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can detach a group/member from the artist.
     *
     * @param User $user
     * @return bool
     */
    public function detachArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can attach any image to the artist.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyImage(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can attach an image to the artist.
     *
     * @param User $user
     * @param Artist $artist
     * @param Image $image
     * @return bool
     */
    public function attachImage(User $user, Artist $artist, Image $image): bool
    {
        if (ArtistImage::where($artist->getKeyName(), $artist->getKey())->where($image->getKeyName(), $image->getKey())->exists()) {
            return false;
        }

        return $user->hasCurrentTeamPermission('artist:update');
    }

    /**
     * Determine whether the user can detach an image from the artist.
     *
     * @param User $user
     * @return bool
     */
    public function detachImage(User $user): bool
    {
        return $user->hasCurrentTeamPermission('artist:update');
    }
}
