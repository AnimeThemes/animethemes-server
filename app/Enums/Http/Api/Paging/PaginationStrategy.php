<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Paging;

/**
 * Enum PaginationStrategy.
 */
enum PaginationStrategy
{
    case NONE;
    case LIMIT;
    case OFFSET;
}
