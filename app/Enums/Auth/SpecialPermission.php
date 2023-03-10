<?php

declare(strict_types=1);

namespace App\Enums\Auth;

use App\Enums\BaseEnum;

/**
 * Class SpecialPermission.
 */
final class SpecialPermission extends BaseEnum
{
    public const BYPASS_API_RATE_LIMITER = 'bypass api rate limiter';

    public const BYPASS_FEATURE_FLAGS = 'bypass feature flags';

    public const VIEW_HORIZON = 'view horizon';

    public const VIEW_NOVA = 'view nova';

    public const VIEW_TELESCOPE = 'view telescope';
}
