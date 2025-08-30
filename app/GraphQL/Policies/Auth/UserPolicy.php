<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\Auth;

use App\Enums\Auth\CrudPermission;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;

class UserPolicy extends BasePolicy
{
    /**
     * @param  array  $args
     */
    public function viewAny(?User $user, array $args = []): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(User::class));
    }

    /**
     * @param  array  $args
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function view(?User $user, array $args = [], ?string $keyName = 'model'): bool
    {
        return $user !== null && $user->can(CrudPermission::VIEW->format(User::class));
    }
}
