<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use App\Policies\BasePolicy;

class AudioPolicy extends BasePolicy
{
    /**
     * Determine whether the user can add a video to the audio.
     */
    public function addVideo(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Video::class));
    }
}
