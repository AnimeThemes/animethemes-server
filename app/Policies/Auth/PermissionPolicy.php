<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\Permission;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class PermissionPolicy extends BasePolicy
{
    public function viewAny(?User $user, mixed $value = null): Response
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Permission::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Permission  $permission
     */
    public function view(?User $user, Model $permission): Response
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Permission::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Permission  $permission
     */
    public function update(User $user, Model $permission): Response
    {
        return $user->can(CrudPermission::UPDATE->format(Permission::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Permission  $permission
     */
    public function delete(User $user, Model $permission): Response
    {
        return $user->can(CrudPermission::DELETE->format(Permission::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Permission  $permission
     */
    public function restore(User $user, Model $permission): Response
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(Permission::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyRole(): Response
    {
        return Response::deny();
    }

    public function attachRole(): Response
    {
        return Response::deny();
    }

    public function detachAnyRole(): Response
    {
        return Response::deny();
    }

    public function detachRole(): Response
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
