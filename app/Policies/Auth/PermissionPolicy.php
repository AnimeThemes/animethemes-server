<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\Permission;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Database\Eloquent\Model;

class PermissionPolicy extends BasePolicy
{
    public function viewAny(?User $user): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Permission::class));
    }

    /**
     * @param  Permission  $permission
     */
    public function view(?User $user, Model $permission): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Permission::class));
    }

    /**
     * @param  Permission  $permission
     */
    public function update(User $user, Model $permission): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Permission::class));
    }

    /**
     * @param  Permission  $permission
     */
    public function delete(User $user, Model $permission): bool
    {
        return $user->can(CrudPermission::DELETE->format(Permission::class));
    }

    /**
     * @param  Permission  $permission
     */
    public function restore(User $user, Model $permission): bool
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(Permission::class));
    }

    /**
     * Determine whether the user can attach any role to the permission.
     */
    public function attachAnyRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a role to the permission.
     */
    public function attachRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach any role from the permission.
     */
    public function detachAnyRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a role from the permission.
     */
    public function detachRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach any user to the permission.
     */
    public function attachAnyUser(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a user to the permission.
     */
    public function attachUser(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach any user from the permission.
     */
    public function detachAnyUser(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a user from the permission.
     */
    public function detachUser(): bool
    {
        return false;
    }
}
