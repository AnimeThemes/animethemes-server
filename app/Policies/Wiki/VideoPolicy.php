<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
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
     * Determine whether the user can attach an entry to the video.
     *
     * @param  User  $user
     * @param  Video  $video
     * @param  AnimeThemeEntry  $entry
     * @return bool
     */
    public function attachAnimeThemeEntry(User $user, Video $video, AnimeThemeEntry $entry): bool
    {
        $attached = AnimeThemeEntryVideo::query()
            ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $video->getKey())
            ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $entry->getKey())
            ->exists();

        return !$attached && $user->can(CrudPermission::UPDATE->format(Video::class));
    }

    /**
     * Determine whether the user can detach an entry from a video.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyAnimeThemeEntry(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Video::class));
    }

    /**
     * Determine whether the user can detach an entry from a video.
     *
     * @param  User  $user
     * @param  Video  $video
     * @param  AnimeThemeEntry  $entry
     * @return bool
     */
    public function detachAnimeThemeEntry(User $user, Video $video, AnimeThemeEntry $entry): bool
    {
        $attached = AnimeThemeEntryVideo::query()
            ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $video->getKey())
            ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $entry->getKey())
            ->exists();

        return $attached && $user->can(CrudPermission::UPDATE->format(Video::class));
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
