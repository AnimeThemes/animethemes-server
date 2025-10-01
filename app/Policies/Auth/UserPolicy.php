<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends BasePolicy
{
    public function viewAny(?User $user, mixed $value = null): Response
    {
        return $user instanceof User && $user->can(CrudPermission::VIEW->format(User::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  User  $userModel
     */
    public function view(?User $user, Model $userModel): Response
    {
        return $user instanceof User && $user->can(CrudPermission::VIEW->format(User::class))
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

    public function addPlaylist(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
