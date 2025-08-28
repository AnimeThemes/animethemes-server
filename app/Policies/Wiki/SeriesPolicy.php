<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Policies\BasePolicy;

class SeriesPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any anime to the series.
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Series::class)) && $user->can(CrudPermission::CREATE->format(Anime::class));
    }

    /**
     * Determine whether the user can detach any anime from the series.
     */
    public function detachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Series::class)) && $user->can(CrudPermission::DELETE->format(Anime::class));
    }
}
