<?php

declare(strict_types=1);

namespace App\Features;

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Laravel\Pennant\Feature;

class AllowDumpDownloading
{
    public function resolve(?User $user): bool
    {
        if ($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value)) {
            return true;
        }

        return Feature::for(null)->value(static::class);
    }
}
