<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class SongPolicy extends BasePolicy
{
    public function attachAnyArtist(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Song::class)) && $user->can(CrudPermission::CREATE->format(Artist::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyArtist(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Song::class)) && $user->can(CrudPermission::DELETE->format(Artist::class))
            ? Response::allow()
            : Response::deny();
    }

    public function addAnimeTheme(User $user): Response
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeTheme::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyExternalResource(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Song::class)) && $user->can(CrudPermission::CREATE->format(ExternalResource::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyExternalResource(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Song::class)) && $user->can(CrudPermission::DELETE->format(ExternalResource::class))
            ? Response::allow()
            : Response::deny();
    }
}
