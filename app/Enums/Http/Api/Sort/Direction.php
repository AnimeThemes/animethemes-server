<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Sort;

/**
 * Enum Direction.
 */
enum Direction: string
{
    case ASCENDING = 'asc';
    case DESCENDING = 'desc';
}
