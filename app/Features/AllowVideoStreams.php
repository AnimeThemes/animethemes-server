<?php

declare(strict_types=1);

namespace App\Features;

use App\Constants\FeatureConstants;
use App\Enums\Auth\SpecialPermission;
use App\Models\Admin\Feature;
use App\Models\Auth\User;

class AllowVideoStreams
{
    public function resolve(?User $user): bool
    {
        if ($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value)) {
            return true;
        }

        return (bool) Feature::query()
            ->where(Feature::ATTRIBUTE_SCOPE, FeatureConstants::NULL_SCOPE)
            ->where(Feature::ATTRIBUTE_NAME, static::class)
            ->value(Feature::ATTRIBUTE_VALUE);
    }
}
