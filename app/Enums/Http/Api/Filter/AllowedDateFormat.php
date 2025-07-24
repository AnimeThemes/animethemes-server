<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

enum AllowedDateFormat: string
{
    case YMDHISU = 'Y-m-d\TH:i:s.u';
    case YMDHIS = 'Y-m-d\TH:i:s';
    case YMDHI = 'Y-m-d\TH:i';
    case YMDH = 'Y-m-d\TH';
    case YMD = 'Y-m-d';
    case YM = 'Y-m';
    case Y = 'Y';
}
