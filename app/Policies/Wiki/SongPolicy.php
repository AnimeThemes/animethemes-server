<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SongPolicy.
 */
class SongPolicy
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
        return $user->hasCurrentTeamPermission('song:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasCurrentTeamPermission('song:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('song:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->hasCurrentTeamPermission('song:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('song:forceDelete');
    }

    /**
     * Determine whether the user can add a theme to the song.
     *
     * @param User $user
     * @return bool
     */
    public function addTheme(User $user): bool
    {
        return $user->hasCurrentTeamPermission('song:create');
    }

    /**
     * Determine whether the user can attach any artist to the song.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('song:update');
    }

    /**
     * Determine whether the user can attach an artist to the song.
     *
     * @param User $user
     * @return bool
     */
    public function attachArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('song:update');
    }

    /**
     * Determine whether the user can detach an artist from the song.
     *
     * @param User $user
     * @return bool
     */
    public function detachArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('song:update');
    }
}
