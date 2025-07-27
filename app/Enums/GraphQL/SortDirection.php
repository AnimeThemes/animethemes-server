<?php

declare(strict_types=1);

namespace App\Enums\GraphQL;

use Illuminate\Support\Str;

enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    /**
     * Apply the reverse engine.
     */
    public static function resolveFromEnumCase(string $enumCase): string
    {
        return Str::endsWith($enumCase, '_DESC')
            ? static::DESC->value
            : static::ASC->value;
    }
}
