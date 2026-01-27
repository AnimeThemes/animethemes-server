<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Sort;

enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';
}
