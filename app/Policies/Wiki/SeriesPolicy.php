<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\AnimeSeries;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SeriesPolicy.
 */
class SeriesPolicy
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
        return $user->hasCurrentTeamPermission('series:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasCurrentTeamPermission('series:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('series:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->hasCurrentTeamPermission('series:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasCurrentTeamPermission('series:forceDelete');
    }

    /**
     * Determine whether the user can attach any anime to the series.
     *
     * @param User $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->hasCurrentTeamPermission('series:update');
    }

    /**
     * Determine whether the user can attach an anime to the series.
     *
     * @param User $user
     * @param Series $series
     * @param Anime $anime
     * @return bool
     */
    public function attachAnime(User $user, Series $series, Anime $anime): bool
    {
        $attached = AnimeSeries::query()
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($series->getKeyName(), $series->getKey())
            ->exists();

        return ! $attached && $user->hasCurrentTeamPermission('series:update');
    }

    /**
     * Determine whether the user can detach an anime from the series.
     *
     * @param User $user
     * @return bool
     */
    public function detachAnime(User $user): bool
    {
        return $user->hasCurrentTeamPermission('series:update');
    }
}
