<?php

declare(strict_types=1);

namespace App\Policies\Auth;

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
        return $user !== null && parent::viewAny($user);
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
        return $user !== null && parent::view($user, $role);
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
     * Determine whether the user can detach any permission from the role.
     *
     * @return bool
     */
    public function detachAnyPermission(): bool
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
     * Determine whether the user can detach any user from the role.
     *
     * @return bool
     */
    public function detachAnyUser(): bool
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
