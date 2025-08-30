<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class GroupPolicy extends BasePolicy
{
    public function addAnimeTheme(User $user): Response
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeTheme::class))
            ? Response::allow()
            : Response::deny();
    }
}
