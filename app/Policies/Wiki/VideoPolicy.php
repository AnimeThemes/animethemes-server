<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Nova\Nova;

/**
 * Class VideoPolicy.
 */
class VideoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return Nova::whenServing(
            fn (): bool => $user !== null && $user->can('view video'),
            fn (): bool => true
        );
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function view(?User $user): bool
    {
        return Nova::whenServing(
            fn (): bool => $user !== null && $user->can('view video'),
            fn (): bool => true
        );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create video');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('update video');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete video');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Video  $video
     * @return bool
     */
    public function restore(User $user, Video $video): bool
    {
        return $video->trashed() && $user->can('restore video');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can('force delete video');
    }

    /**
     * Determine whether the user can attach any entry to a video.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyAnimeThemeEntry(User $user): bool
    {
        return $user->can('update video');
    }

    /**
     * Determine whether the user can attach an entry to a video.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnimeThemeEntry(User $user): bool
    {
        return $user->can('update video');
    }

    /**
     * Determine whether the user can detach an entry from a video.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnimeThemeEntry(User $user): bool
    {
        return $user->can('update video');
    }

    /**
     * Determine whether the user can add a track to the video.
     *
     * @param  User  $user
     * @return bool
     */
    public function addTrack(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}
