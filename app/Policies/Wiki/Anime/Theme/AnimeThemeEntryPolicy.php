<?php

declare(strict_types=1);

namespace App\Policies\Wiki\Anime\Theme;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Policies\BasePolicy;

class AnimeThemeEntryPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any video to the entry.
     */
    public function attachAnyVideo(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(AnimeThemeEntry::class)) && $user->can(CrudPermission::CREATE->format(Video::class));
    }

    /**
     * Determine whether the user can detach any video from the entry.
     */
    public function detachAnyVideo(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(AnimeThemeEntry::class)) && $user->can(CrudPermission::DELETE->format(Video::class));
    }
}
