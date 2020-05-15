<?php

namespace App\Policies;

use App\Models\Entry;
use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Video  $video
     * @return mixed
     */
    public function view(User $user, Video $video)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Video  $video
     * @return mixed
     */
    public function update(User $user, Video $video)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Video  $video
     * @return mixed
     */
    public function delete(User $user, Video $video)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Video  $video
     * @return mixed
     */
    public function restore(User $user, Video $video)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Video  $video
     * @return mixed
     */
    public function forceDelete(User $user, Video $video)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can attach any entry to a video.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Video  $video
     * @return mixed
     */
    public function attachAnyEntry(User $user, Video $video)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can attach an entry to a video.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Video  $video
     * @param  \App\Models\Entry  $entry
     * @return mixed
     */
    public function attachEntry(User $user, Video $video, Entry $entry)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can detach an entry to a video.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Video  $video
     * @return mixed
     */
    public function detachEntry(User $user, Video $video, Entry $entry)
    {
        return $user->isAdmin();
    }
}
