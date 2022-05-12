<?php

declare(strict_types=1);

namespace App\Policies\Wiki\Anime\Theme;

use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\AnimeThemeEntryVideo;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AnimeThemeEntryPolicy.
 */
class AnimeThemeEntryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view anime theme entry');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function view(User $user): bool
    {
        return $user->can('view anime theme entry');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create anime theme entry');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update anime theme entry');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete anime theme entry');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->can('restore anime theme entry');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can('force delete anime theme entry');
    }

    /**
     * Determine whether the user can attach any video to the entry.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyVideo(User $user): bool
    {
        return $user->can('update anime theme entry');
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

        return ! $attached && $user->can('update anime theme entry');
    }

    /**
     * Determine whether the user can detach a video from the entry.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachVideo(User $user): bool
    {
        return $user->can('update anime theme entry');
    }
}
