<?php

declare(strict_types=1);

namespace App\Policies\Wiki\Anime;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Policies\BasePolicy;

/**
 * Class AnimeThemePolicy.
 */
class AnimeThemePolicy extends BasePolicy
{
    /**
     * Determine whether the user can add a entry to the theme.
     *
     * @param  User  $user
     * @return bool
     */
    public function addAnyAnimeThemeEntry(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeThemeEntry::class));
    }
}
