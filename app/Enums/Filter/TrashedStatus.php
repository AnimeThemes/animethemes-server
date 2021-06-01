<?php

declare(strict_types=1);

namespace App\Enums\Filter;

use BenSampo\Enum\Enum;

/**
 * Class TrashedStatus.
 */
final class TrashedStatus extends Enum
{
    public const WITH = 'with';
    public const WITHOUT = 'without';
    public const ONLY = 'only';
}
