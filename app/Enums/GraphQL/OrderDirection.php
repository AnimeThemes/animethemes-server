<?php

declare(strict_types=1);

namespace App\Enums\GraphQL;

enum OrderDirection: int
{
    case ASC = 0;
    case DESC = 1;
}
