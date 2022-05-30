<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

use App\Enums\BaseEnum;

/**
 * Class TrashedStatus.
 */
final class TrashedStatus extends BaseEnum
{
    public const WITH = 'with';
    public const WITHOUT = 'without';
    public const ONLY = 'only';
}
