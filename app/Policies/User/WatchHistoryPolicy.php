<?php

declare(strict_types=1);

namespace App\Policies\User;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\User\WatchHistory;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class WatchHistoryPolicy extends BasePolicy
{
    /**
     * @param  WatchHistory  $watchHistory
     */
    public function delete(User $user, Model $watchHistory): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return $watchHistory->user()->is($user) && $user->can(CrudPermission::DELETE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }
}
