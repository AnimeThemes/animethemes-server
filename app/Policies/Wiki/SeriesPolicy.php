<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Nova\Nova;

/**
 * Class SeriesPolicy.
 */
class SeriesPolicy
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
            fn (): bool => $user !== null && $user->can('view series'),
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
            fn (): bool => $user !== null && $user->can('view series'),
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
        return $user->can('create series');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Series  $series
     * @return bool
     */
    public function update(User $user, Series $series): bool
    {
        return ! $series->trashed() && $user->can('update series');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Series  $series
     * @return bool
     */
    public function delete(User $user, Series $series): bool
    {
        return ! $series->trashed() && $user->can('delete series');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Series  $series
     * @return bool
     */
    public function restore(User $user, Series $series): bool
    {
        return $series->trashed() && $user->can('restore series');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can('force delete series');
    }

    /**
     * Determine whether the user can attach any anime to the series.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can('update series');
    }

    /**
     * Determine whether the user can attach an anime to the series.
     *
     * @param  User  $user
     * @param  Series  $series
     * @param  Anime  $anime
     * @return bool
     */
    public function attachAnime(User $user, Series $series, Anime $anime): bool
    {
        $attached = AnimeSeries::query()
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($series->getKeyName(), $series->getKey())
            ->exists();

        return ! $attached && $user->can('update series');
    }

    /**
     * Determine whether the user can detach an anime from the series.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnime(User $user): bool
    {
        return $user->can('update series');
    }
}
