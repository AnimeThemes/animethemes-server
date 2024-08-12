<?php

declare(strict_types=1);

namespace App\Policies\Wiki\Anime\Theme;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use App\Policies\BasePolicy;

/**
 * Class AnimeThemeEntryPolicy.
 */
class AnimeThemeEntryPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any video to the entry.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyVideo(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can attach a video to the entry.
     *
     * @param  User  $user
     * @param  AnimeThemeEntry  $entry
     * @param  Video  $video
     * @return bool
     */
    public function attachVideo(User $user, AnimeThemeEntry $entry, Video $video): bool
    {
        $attached = AnimeThemeEntryVideo::query()
            ->where($entry->getKeyName(), $entry->getKey())
            ->where($video->getKeyName(), $video->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can detach a video from the entry.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachVideo(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeThemeEntry::class));
    }
}
