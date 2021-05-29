<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnnouncementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasCurrentTeamPermission('announcement:read');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Announcement $announcement
     * @return mixed
     */
    public function view(User $user, Announcement $announcement)
    {
        return $user->hasCurrentTeamPermission('announcement:read');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasCurrentTeamPermission('announcement:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Announcement $announcement
     * @return mixed
     */
    public function update(User $user, Announcement $announcement)
    {
        return $user->hasCurrentTeamPermission('announcement:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Announcement $announcement
     * @return mixed
     */
    public function delete(User $user, Announcement $announcement)
    {
        return $user->hasCurrentTeamPermission('announcement:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Announcement $announcement
     * @return mixed
     */
    public function restore(User $user, Announcement $announcement)
    {
        return $user->hasCurrentTeamPermission('announcement:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Announcement $announcement
     * @return mixed
     */
    public function forceDelete(User $user, Announcement $announcement)
    {
        return $user->hasCurrentTeamPermission('announcement:forceDelete');
    }
}
