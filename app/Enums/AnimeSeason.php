<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Class AnimeSeason
 * @package App\Enums
 */
final class AnimeSeason extends Enum implements LocalizedEnum
{
    public const WINTER = 0;
    public const SPRING = 1;
    public const SUMMER = 2;
    public const FALL = 3;
}
