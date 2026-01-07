<?php

declare(strict_types=1);

namespace App\Policies\User;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\User\Like;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class LikePolicy extends BasePolicy
{
    public function create(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Like::class))
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user, mixed $value): Response
    {
        return $user->can(CrudPermission::DELETE->format(Like::class))
            ? Response::allow()
            : Response::deny();
    }
}
