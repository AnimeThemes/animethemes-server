<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class UserPolicy.
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can(CrudPermission::VIEW()->format(User::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function view(User $user): bool
    {
        return $user->can(CrudPermission::VIEW()->format(User::class));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(CrudPermission::CREATE()->format(User::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE()->format(User::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can(CrudPermission::DELETE()->format(User::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::RESTORE()->format(User::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE()->format(User::class));
    }

    /**
     * Determine whether the user can attach any role to the user.
     *
     * @return bool
     */
    public function attachAnyRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a role to the user.
     *
     * @return bool
     */
    public function attachRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a role from the user.
     *
     * @return bool
     */
    public function detachRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach any permission to the user.
     *
     * @return bool
     */
    public function attachAnyPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a permission to the user.
     *
     * @return bool
     */
    public function attachPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a permission from the user.
     *
     * @return bool
     */
    public function detachPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can add a playlist to the user.
     *
     * @param  User  $user
     * @return bool
     */
    public function addPlaylist(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}
