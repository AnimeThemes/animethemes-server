<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class RolePolicy extends BasePolicy
{
    public function viewAny(?User $user): Response
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Role::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Role  $role
     */
    public function view(?User $user, Model $role): Response
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Role::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyPermission(): Response
    {
        return Response::deny();
    }

    public function attachPermission(): Response
    {
        return Response::deny();
    }

    public function detachAnyPermission(): Response
    {
        return Response::deny();
    }

    public function detachPermission(): Response
    {
        return Response::deny();
    }

    public function attachAnyUser(): Response
    {
        return Response::deny();
    }

    public function attachUser(): Response
    {
        return Response::deny();
    }

    public function detachAnyUser(): Response
    {
        return Response::deny();
    }

    public function detachUser(): Response
    {
        return Response::deny();
    }
}
