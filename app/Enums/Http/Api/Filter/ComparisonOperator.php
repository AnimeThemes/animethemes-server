<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

use App\Concerns\Enums\CoercesInstances;

enum ComparisonOperator: string
{
    use CoercesInstances;

    case EQ = '=';
    case NE = '<>';
    case LT = '<';
    case GT = '>';
    case LTE = '<=';
    case GTE = '>=';
    case LIKE = 'like';
    case NOTLIKE = 'not like';
}
