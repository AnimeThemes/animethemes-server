<?php

namespace App\Policies;

use App\Models\Synonym;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SynonymPolicy
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
     * @param  \App\Models\Synonym  $synonym
     * @return mixed
     */
    public function view(User $user, Synonym $synonym)
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
     * @param  \App\Models\Synonym  $synonym
     * @return mixed
     */
    public function update(User $user, Synonym $synonym)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Synonym  $synonym
     * @return mixed
     */
    public function delete(User $user, Synonym $synonym)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Synonym  $synonym
     * @return mixed
     */
    public function restore(User $user, Synonym $synonym)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Synonym  $synonym
     * @return mixed
     */
    public function forceDelete(User $user, Synonym $synonym)
    {
        return $user->isAdmin();
    }
}
