<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;

class AudioPolicy extends BasePolicy
{
    public function addVideo(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(Video::class))
            ? Response::allow()
            : Response::deny();
    }
}
