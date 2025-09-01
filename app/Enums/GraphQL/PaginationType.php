<?php

declare(strict_types=1);

namespace App\Enums\GraphQL;

enum PaginationType
{
    case NONE;
    case SIMPLE;
    case PAGINATION;
    case CONNECTION;
}
