<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\Role;
use App\Models\Auth\User;

/**
 * Class RolePolicy.
 */
class RolePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @return bool
     */
    public function viewAny(?User $user, ?array $injected = null): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Role::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function view(?User $user, ?array $injected = null): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Role::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function update(User $user, array $injected): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Role::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function delete(User $user, array $injected): bool
    {
        return $user->can(CrudPermission::DELETE->format(Role::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function restore(User $user, array $injected): bool
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(Role::class));
    }
}
