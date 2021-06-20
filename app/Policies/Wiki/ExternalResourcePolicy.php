<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ExternalResourcePolicy.
 */
class ExternalResourcePolicy
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
        return $user->hasCurrentTeamPermission('resource:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasCurrentTeamPermission('resource:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('resource:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->hasCurrentTeamPermission('resource:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('resource:forceDelete');
    }

    /**
     * Determine whether the user can attach any artist to the resource.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('resource:update');
    }

    /**
     * Determine whether the user can attach an artist to the resource.
     *
     * @param User $user
     * @return bool
     */
    public function attachArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('resource:update');
    }

    /**
     * Determine whether the user can detach an artist from the resource.
     *
     * @param User $user
     * @return bool
     */
    public function detachArtist(User $user): bool
    {
        return $user->hasCurrentTeamPermission('resource:update');
    }

    /**
     * Determine whether the user can attach any anime to the resource.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->hasCurrentTeamPermission('resource:update');
    }

    /**
     * Determine whether the user can attach an anime to the resource.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnime(User $user): bool
    {
        return $user->hasCurrentTeamPermission('resource:update');
    }

    /**
     * Determine whether the user can detach an anime from the resource.
     *
     * @param User $user
     * @return bool
     */
    public function detachAnime(User $user): bool
    {
        return $user->hasCurrentTeamPermission('resource:update');
    }
}
