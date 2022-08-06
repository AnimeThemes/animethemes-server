<?php

declare(strict_types=1);

namespace App\Enums\Actions;

use App\Enums\BaseEnum;

/**
 * Class ActionStatus.
 *
 * @method static static PASSED()
 * @method static static FAILED()
 * @method static static SKIPPED()
 */
final class ActionStatus extends BaseEnum
{
    public const PASSED = 0;
    public const FAILED = 1;
    public const SKIPPED = 2;
}
