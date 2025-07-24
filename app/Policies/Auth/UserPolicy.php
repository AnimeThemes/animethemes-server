<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(User::class));
    }

    /**
     * Determine whether the user can view the model.
     *Model.
     */
    public function view(?User $user, Model $userModel): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(User::class));
    }

    /**
     * Determine whether the user can attach any role to the user.
     */
    public function attachAnyRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a role to the user.
     */
    public function attachRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach any role from the user.
     */
    public function detachAnyRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a role from the user.
     */
    public function detachRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach any permission to the user.
     */
    public function attachAnyPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a permission to the user.
     */
    public function attachPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach any permission from the user.
     */
    public function detachAnyPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a permission from the user.
     */
    public function detachPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can add a playlist to the user.
     */
    public function addPlaylist(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }
}
