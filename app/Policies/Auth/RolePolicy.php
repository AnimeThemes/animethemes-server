<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class RolePolicy.
 */
class RolePolicy
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
        return $user->can(CrudPermission::VIEW->format(Role::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function view(User $user): bool
    {
        return $user->can(CrudPermission::VIEW->format(Role::class));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Role::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Role::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Role::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(Role::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(Role::class));
    }

    /**
     * Determine whether the user can attach any permission to the role.
     *
     * @return bool
     */
    public function attachAnyPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a permission to the role.
     *
     * @return bool
     */
    public function attachPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a permission from the role.
     *
     * @return bool
     */
    public function detachPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach any user to the role.
     *
     * @return bool
     */
    public function attachAnyUser(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a user to the role.
     *
     * @return bool
     */
    public function attachUser(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a user from the role.
     *
     * @return bool
     */
    public function detachUser(): bool
    {
        return false;
    }
}
