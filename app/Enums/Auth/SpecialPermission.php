<?php

declare(strict_types=1);

namespace App\Enums\Auth;

/**
 * Enum SpecialPermission.
 */
enum SpecialPermission: string
{
    case BYPASS_API_RATE_LIMITER = 'bypass api rate limiter';

    case BYPASS_FEATURE_FLAGS = 'bypass feature flags';

    case VIEW_FILAMENT = 'view filament';

    case VIEW_HORIZON = 'view horizon';

    case VIEW_PULSE = 'view pulse';

    case REVALIDATE_PAGES = 'revalidate pages';
}
