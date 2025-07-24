<?php

declare(strict_types=1);

namespace App\Enums\Actions;

enum ActionStatus
{
    case PASSED;
    case FAILED;
    case SKIPPED;
}
