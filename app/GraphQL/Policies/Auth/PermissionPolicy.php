<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\Auth;

use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;

/**
 * Class PermissionPolicy.
 */
class PermissionPolicy extends BasePolicy
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
        return $user !== null && parent::viewAny($user, $injected);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = 'id'): bool
    {
        return $user !== null && parent::view($user, $injected, $keyName);
    }
}
