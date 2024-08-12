<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RolePolicy.
 */
class RolePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return $user->can(CrudPermission::VIEW->format(Role::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  Role  $role
     * @return bool
     */
    public function view(?User $user, Model $role): bool
    {
        return $user->can(CrudPermission::VIEW->format(Role::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     * 
     * @noinspection PhpUnusedParameterInspection
     */
    public function update(User $user, Model $role): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Role::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     * 
     * @noinspection PhpUnusedParameterInspection
     */
    public function delete(User $user, Model $role): bool
    {
        return $user->can(CrudPermission::DELETE->format(Role::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return bool
     * 
     * @noinspection PhpUnusedParameterInspection
     */
    public function restore(User $user, Model $role): bool
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(Role::class));
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
