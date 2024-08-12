<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use App\Policies\BasePolicy;

/**
 * Class SeriesPolicy.
 */
class SeriesPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any anime to the series.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Series::class));
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

        return !$attached && $user->can(CrudPermission::UPDATE->format(Series::class));
    }

    /**
     * Determine whether the user can detach an anime from the series.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Series::class));
    }
}
