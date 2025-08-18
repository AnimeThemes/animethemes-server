<?php

declare(strict_types=1);

namespace App\Enums\GraphQL;

enum SortType: int
{
    case NONE = 0;
    case ROOT = 1;
    case AGGREGATE = 2;
    case RELATION = 3;
}
