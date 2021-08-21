<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\AnimeStudio;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class StudioPolicy.
 */
class StudioPolicy
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
        return $user->hasCurrentTeamPermission('studio:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasCurrentTeamPermission('studio:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('studio:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->hasCurrentTeamPermission('studio:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('studio:forceDelete');
    }

    /**
     * Determine whether the user can attach any anime to the studio.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->hasCurrentTeamPermission('studio:update');
    }

    /**
     * Determine whether the user can attach an anime to the studio.
     *
     * @param User $user
     * @param Studio $studio
     * @param Anime $anime
     * @return bool
     */
    public function attachAnime(User $user, Studio $studio, Anime $anime): bool
    {
        $attached = AnimeStudio::query()
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($studio->getKeyName(), $studio->getKey())
            ->exists();

        return ! $attached && $user->hasCurrentTeamPermission('studio:update');
    }

    /**
     * Determine whether the user can detach an anime from the studio.
     *
     * @param User $user
     * @return bool
     */
    public function detachAnime(User $user): bool
    {
        return $user->hasCurrentTeamPermission('studio:update');
    }
}
