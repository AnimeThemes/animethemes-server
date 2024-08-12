<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use App\Policies\BasePolicy;

/**
 * Class AudioPolicy.
 */
class AudioPolicy extends BasePolicy
{
    /**
     * Determine whether the user can add a video to the audio.
     *
     * @param  User  $user
     * @return bool
     */
    public function addVideo(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Audio::class));
    }
}
