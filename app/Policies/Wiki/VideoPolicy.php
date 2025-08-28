<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Policies\BasePolicy;

class VideoPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any entry to a video.
     */
    public function attachAnyAnimeThemeEntry(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Video::class)) && $user->can(CrudPermission::CREATE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can detach any entry from a video.
     */
    public function detachAnyAnimeThemeEntry(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Video::class)) && $user->can(CrudPermission::DELETE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can add a track to the video.
     */
    public function addTrack(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }
}
