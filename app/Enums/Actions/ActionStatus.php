<?php

declare(strict_types=1);

namespace App\Enums\Actions;

/**
 * Enum ActionStatus.
 */
enum ActionStatus
{
    case PASSED;
    case FAILED;
    case SKIPPED;
}
