<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Class VideoOverlap
 * @package App\Enums
 */
final class VideoOverlap extends Enum implements LocalizedEnum
{
    public const NONE = 0;
    public const TRANS = 1;
    public const OVER = 2;
}
