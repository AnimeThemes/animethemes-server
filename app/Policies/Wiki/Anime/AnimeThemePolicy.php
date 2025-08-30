<?php

declare(strict_types=1);

namespace App\Policies\Wiki\Anime;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class AnimeThemePolicy extends BasePolicy
{
    public function addAnyAnimeThemeEntry(User $user): Response
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeThemeEntry::class))
            ? Response::allow()
            : Response::deny();
    }
}
