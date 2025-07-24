<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\User;

use App\Enums\Auth\CrudPermission;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;
use App\Models\User\Like;

class LikePolicy extends BasePolicy
{
    /**
     * Determine whether the user can delete the model.
     *
     * @param  array  $injected
     */
    public function delete(User $user, array $injected, ?string $keyName = 'id'): bool
    {
        return $user->can(CrudPermission::DELETE->format(Like::class));
    }
}
