<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class ArtistPolicy extends BasePolicy
{
    public function attachAnySong(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Artist::class)) && $user->can(CrudPermission::CREATE->format(Song::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnySong(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Artist::class)) && $user->can(CrudPermission::DELETE->format(Song::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyExternalResource(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Artist::class)) && $user->can(CrudPermission::CREATE->format(ExternalResource::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyExternalResource(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Artist::class)) && $user->can(CrudPermission::DELETE->format(ExternalResource::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyArtist(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Artist::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyArtist(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Artist::class))
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyImage(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Artist::class)) && $user->can(CrudPermission::CREATE->format(Image::class))
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyImage(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(Artist::class)) && $user->can(CrudPermission::DELETE->format(Image::class))
            ? Response::allow()
            : Response::deny();
    }
}
