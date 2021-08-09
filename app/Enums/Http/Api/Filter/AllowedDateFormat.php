<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

use App\Enums\BaseEnum;

/**
 * Class AllowedDateFormat.
 */
final class AllowedDateFormat extends BaseEnum
{
    public const YMDHISU = 'Y-m-d\TH:i:s.u';
    public const YMDHIS = 'Y-m-d\TH:i:s';
    public const YMDHI = 'Y-m-d\TH:i';
    public const YMDH = 'Y-m-d\TH';
    public const YMD = 'Y-m-d';
    public const YM = 'Y-m';
    public const Y = 'Y';
}
