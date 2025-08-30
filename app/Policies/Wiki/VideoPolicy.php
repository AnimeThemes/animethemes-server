<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class VideoPolicy extends BasePolicy
{
    public function attachAnyAnimeThemeEntry(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Video::class)) && $user->can(CrudPermission::CREATE->format(AnimeThemeEntry::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyAnimeThemeEntry(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Video::class)) && $user->can(CrudPermission::DELETE->format(AnimeThemeEntry::class))
            ? Response::allow()
            : Response::deny();
    }

    public function addTrack(User $user): Response
    {
        return $user->hasRole(RoleEnum::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
