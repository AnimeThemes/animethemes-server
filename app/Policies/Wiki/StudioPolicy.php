<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class StudioPolicy extends BasePolicy
{
    public function attachAnyAnime(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Studio::class)) && $user->can(CrudPermission::CREATE->format(Anime::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyAnime(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Studio::class)) && $user->can(CrudPermission::DELETE->format(Anime::class))
            ? Response::allow()
            : Response::deny();
    }
}
