<?php

declare(strict_types=1);

namespace App\Features;

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Laravel\Pennant\Feature;

/**
 * Class AllowReport.
 */
class AllowReport
{
    /**
     * Resolve feature for scope.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function resolve(?User $user): bool
    {
        if (! empty($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value))) {
            return true;
        }

        return Feature::for(null)->value(static::class);
    }
}
