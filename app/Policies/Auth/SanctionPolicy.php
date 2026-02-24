<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\Sanction;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class SanctionPolicy extends BasePolicy
{
    public function viewAny(?User $user, mixed $value = null): Response
    {
        return $user?->can(CrudPermission::VIEW->format(Sanction::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Sanction  $Sanction
     */
    public function view(?User $user, Model $Sanction): Response
    {
        return $user?->can(CrudPermission::VIEW->format(Sanction::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyProhibition(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function attachProhibition(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyProhibition(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachProhibition(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyUser(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function attachUser(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyUser(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachUser(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
