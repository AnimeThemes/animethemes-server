<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Field;

enum AggregateFunction: string
{
    case AVG = 'avg';
    case COUNT = 'count';
    case EXISTS = 'exists';
    case MAX = 'max';
    case MIN = 'min';
    case SUM = 'sum';
}
