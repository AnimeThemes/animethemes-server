<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Policies\BasePolicy;

/**
 * Class GroupPolicy.
 */
class GroupPolicy extends BasePolicy
{
    /**
     * Determine whether the user can add a theme to the group.
     *
     * @param  User  $user
     * @return bool
     */
    public function addAnimeTheme(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeTheme::class));
    }
}
