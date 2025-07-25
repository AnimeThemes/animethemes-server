<?php

declare(strict_types=1);

namespace App\Enums\GraphQL;

enum SortType: int
{
    case ROOT = 0;
    case AGGREGATE = 1;
}
