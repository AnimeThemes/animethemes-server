<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Database\Eloquent\Model;

class RolePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Role::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  Role  $role
     */
    public function view(?User $user, Model $role): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Role::class));
    }

    /**
     * Determine whether the user can attach any permission to the role.
     */
    public function attachAnyPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a permission to the role.
     */
    public function attachPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach any permission from the role.
     */
    public function detachAnyPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a permission from the role.
     */
    public function detachPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach any user to the role.
     */
    public function attachAnyUser(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a user to the role.
     */
    public function attachUser(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach any user from the role.
     */
    public function detachAnyUser(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a user from the role.
     */
    public function detachUser(): bool
    {
        return false;
    }
}
