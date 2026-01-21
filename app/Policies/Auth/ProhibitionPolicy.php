<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\Prohibition;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class ProhibitionPolicy extends BasePolicy
{
    public function viewAny(?User $user, mixed $value = null): Response
    {
        return $user instanceof User && $user->can(CrudPermission::VIEW->format(Prohibition::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Prohibition  $Prohibition
     */
    public function view(?User $user, Model $Prohibition): Response
    {
        return $user instanceof User && $user->can(CrudPermission::VIEW->format(Prohibition::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Prohibition  $Prohibition
     */
    public function update(User $user, Model $Prohibition): Response
    {
        return $user->can(CrudPermission::UPDATE->format(Prohibition::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Prohibition  $Prohibition
     */
    public function delete(User $user, Model $Prohibition): Response
    {
        return $user->can(CrudPermission::DELETE->format(Prohibition::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Prohibition  $Prohibition
     */
    public function restore(User $user, Model $Prohibition): Response
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(Prohibition::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnySanction(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function attachSanction(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnySanction(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachSanction(User $user): Response
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
