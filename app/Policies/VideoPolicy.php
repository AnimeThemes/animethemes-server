<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class VideoPolicy
 * @package App\Policies
 */
class VideoPolicy
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
        return $user->hasCurrentTeamPermission('video:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasCurrentTeamPermission('video:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('video:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->hasCurrentTeamPermission('video:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('video:forceDelete');
    }

    /**
     * Determine whether the user can attach any entry to a video.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyEntry(User $user): bool
    {
        return $user->hasCurrentTeamPermission('videoentry:create');
    }

    /**
     * Determine whether the user can attach an entry to a video.
     *
     * @param User $user
     * @return bool
     */
    public function attachEntry(User $user): bool
    {
        return $user->hasCurrentTeamPermission('videoentry:create');
    }

    /**
     * Determine whether the user can detach an entry from a video.
     *
     * @param User $user
     * @return bool
     */
    public function detachEntry(User $user): bool
    {
        return $user->hasCurrentTeamPermission('videoentry:delete');
    }
}
