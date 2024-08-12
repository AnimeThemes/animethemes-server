<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use App\Policies\BasePolicy;

/**
 * Class VideoPolicy.
 */
class VideoPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any entry to a video.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyAnimeThemeEntry(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Video::class));
    }

    /**
     * Determine whether the user can attach an entry to a video.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnimeThemeEntry(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Video::class));
    }

    /**
     * Determine whether the user can detach an entry from a video.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnimeThemeEntry(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Video::class));
    }

    /**
     * Determine whether the user can add a track to the video.
     *
     * @param  User  $user
     * @return bool
     */
    public function addTrack(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }
}
