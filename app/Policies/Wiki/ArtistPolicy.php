<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class ArtistPolicy extends BasePolicy
{
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
}
