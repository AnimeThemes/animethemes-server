<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Class AnimeSeason.
 */
final class AnimeSeason extends Enum implements LocalizedEnum
{
    public const WINTER = 0;
    public const SPRING = 1;
    public const SUMMER = 2;
    public const FALL = 3;
}
