<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class RolePolicy extends BasePolicy
{
    public function viewAny(?User $user, mixed $value = null): Response
    {
        return $user?->can(CrudPermission::VIEW->format(Role::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Role  $role
     */
    public function view(?User $user, Model $role): Response
    {
        return $user?->can(CrudPermission::VIEW->format(Role::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyPermission(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function attachPermission(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyPermission(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachPermission(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyUser(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function attachUser(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyUser(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachUser(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
