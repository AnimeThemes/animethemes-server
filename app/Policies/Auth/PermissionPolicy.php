<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\Permission;
use App\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class PermissionPolicy.
 */
class PermissionPolicy
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
        return $user->can(CrudPermission::VIEW()->format(Permission::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function view(User $user): bool
    {
        return $user->can(CrudPermission::VIEW()->format(Permission::class));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(CrudPermission::CREATE()->format(Permission::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE()->format(Permission::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can(CrudPermission::DELETE()->format(Permission::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::RESTORE()->format(Permission::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE()->format(Permission::class));
    }

    /**
     * Determine whether the user can attach any role to the permission.
     *
     * @return bool
     */
    public function attachAnyRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a role to the permission.
     *
     * @return bool
     */
    public function attachRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a role from the permission.
     *
     * @return bool
     */
    public function detachRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach any user to the permission.
     *
     * @return bool
     */
    public function attachAnyUser(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a user to the permission.
     *
     * @return bool
     */
    public function attachUser(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a user from the permission.
     *
     * @return bool
     */
    public function detachUser(): bool
    {
        return false;
    }
}
