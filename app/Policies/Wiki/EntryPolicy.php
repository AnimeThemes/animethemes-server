<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;
use App\Pivots\VideoEntry;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class EntryPolicy.
 */
class EntryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return bool
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return bool
     */
    public function view(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasCurrentTeamPermission('entry:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasCurrentTeamPermission('entry:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('entry:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->hasCurrentTeamPermission('entry:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('entry:forceDelete');
    }

    /**
     * Determine whether the user can attach any video to the entry.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyVideo(User $user): bool
    {
        return $user->hasCurrentTeamPermission('entry:update');
    }

    /**
     * Determine whether the user can attach a video to the entry.
     *
     * @param User $user
     * @param Entry $entry
     * @param Video $video
     * @return bool
     */
    public function attachVideo(User $user, Entry $entry, Video $video): bool
    {
        if (VideoEntry::where($entry->getKeyName(), $entry->getKey())->where($video->getKeyName(), $video->getKey())->exists()) {
            return false;
        }

        return $user->hasCurrentTeamPermission('entry:update');
    }

    /**
     * Determine whether the user can detach a video from the entry.
     *
     * @param User $user
     * @return bool
     */
    public function detachVideo(User $user): bool
    {
        return $user->hasCurrentTeamPermission('entry:update');
    }
}
