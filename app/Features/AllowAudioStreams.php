<?php

namespace App\Features;

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Laravel\Pennant\Feature;

class AllowAudioStreams
{
    public function resolve(?User $user): bool
    {
        if (! empty($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS))) {
            return true;
        }

        return Feature::for(null)->value(static::class);
    }
}