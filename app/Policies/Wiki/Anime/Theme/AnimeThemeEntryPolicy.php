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
        return $user->can(CrudPermission::CREATE->format(AnimeThemeEntry::class)) && $user->can(CrudPermission::CREATE->format(Video::class));
    }

    /**
     * Determine whether the user can attach an entry to the video.
     *
     * @param  User  $user
     * @param  Video  $video
     * @param  AnimeThemeEntry  $entry
     * @return bool
     */
    public function attachVideo(User $user, Video $video, AnimeThemeEntry $entry): bool
    {
        $attached = AnimeThemeEntryVideo::query()
            ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $video->getKey())
            ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $entry->getKey())
            ->exists();

        return !$attached
            && $user->can(CrudPermission::CREATE->format(AnimeThemeEntry::class))
            && $user->can(CrudPermission::CREATE->format(Video::class));
    }

    /**
     * Determine whether the user can detach any video from the entry.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyVideo(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(AnimeThemeEntry::class)) && $user->can(CrudPermission::DELETE->format(Video::class));
    }
}
