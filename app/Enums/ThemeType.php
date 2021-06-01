<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Class ThemeType.
 */
final class ThemeType extends Enum implements LocalizedEnum
{
    public const OP = 0;
    public const ED = 1;
}
