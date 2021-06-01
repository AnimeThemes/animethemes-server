<?php declare(strict_types=1);

namespace App\Enums\Filter;

use BenSampo\Enum\Enum;

/**
 * Class AllowedDateFormat
 * @package App\Enums\Filter
 */
final class AllowedDateFormat extends Enum
{
    public const WITH_MICRO = 'Y-m-d\TH:i:s.u';
    public const WITH_SEC = 'Y-m-d\TH:i:s';
    public const WITH_MIN = 'Y-m-d\TH:i';
    public const WITH_HOUR = 'Y-m-d\TH';
    public const WITH_DAY = 'Y-m-d';
    public const WITH_MONTH = 'Y-m';
    public const WITH_YEAR = 'Y';
}
