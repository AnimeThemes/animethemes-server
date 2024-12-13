<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserPolicy.
 */
class UserPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(User::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  User  $userModel
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function view(?User $user, Model $userModel): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(User::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  User  $userModel
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function update(User $user, Model $userModel): bool
    {
        return $user->can(CrudPermission::UPDATE->format(User::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  User  $userModel
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function delete(User $user, Model $userModel): bool
    {
        return $user->can(CrudPermission::DELETE->format(User::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  User  $userModel
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function restore(User $user, Model $userModel): bool
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(User::class));
    }

    /**
     * Determine whether the user can attach any role to the user.
     *
     * @return bool
     */
    public function attachAnyRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a role to the user.
     *
     * @return bool
     */
    public function attachRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach any role from the user.
     *
     * @return bool
     */
    public function detachAnyRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a role from the user.
     *
     * @return bool
     */
    public function detachRole(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach any permission to the user.
     *
     * @return bool
     */
    public function attachAnyPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can attach a permission to the user.
     *
     * @return bool
     */
    public function attachPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach any permission from the user.
     *
     * @return bool
     */
    public function detachAnyPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can detach a permission from the user.
     *
     * @return bool
     */
    public function detachPermission(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can add a playlist to the user.
     *
     * @param  User  $user
     * @return bool
     */
    public function addPlaylist(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }
}
