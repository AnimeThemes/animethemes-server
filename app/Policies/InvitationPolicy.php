<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvitationPolicy
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
        return $user->hasCurrentTeamPermission('invitation:read');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Invitation $invitation
     * @return mixed
     */
    public function view(User $user, Invitation $invitation)
    {
        return $user->hasCurrentTeamPermission('invitation:read');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasCurrentTeamPermission('invitation:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Invitation $invitation
     * @return mixed
     */
    public function update(User $user, Invitation $invitation)
    {
        return $user->hasCurrentTeamPermission('invitation:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Invitation $invitation
     * @return mixed
     */
    public function delete(User $user, Invitation $invitation)
    {
        return $user->hasCurrentTeamPermission('invitation:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Invitation $invitation
     * @return mixed
     */
    public function restore(User $user, Invitation $invitation)
    {
        return $user->hasCurrentTeamPermission('invitation:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Invitation $invitation
     * @return mixed
     */
    public function forceDelete(User $user, Invitation $invitation)
    {
        return $user->hasCurrentTeamPermission('invitation:forceDelete');
    }
}
