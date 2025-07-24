<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\Role;
use App\Models\Auth\User;

class RolePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  array|null  $injected
     */
    public function viewAny(?User $user, ?array $injected = null): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Role::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  array|null  $injected
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = 'id'): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(Role::class));
    }
}
