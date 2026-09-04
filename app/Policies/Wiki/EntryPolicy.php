<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class EntryPolicy extends BasePolicy
{
    public function attachAnyVideo(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Entry::class)) && $user->can(CrudPermission::CREATE->format(Video::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyVideo(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Entry::class)) && $user->can(CrudPermission::DELETE->format(Video::class))
            ? Response::allow()
            : Response::deny();
    }
}
