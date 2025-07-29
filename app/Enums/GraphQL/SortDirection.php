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
            ? self::DESC->value
            : self::ASC->value;
    }

    /**
     * Build the enum case for the asc direction.
     * Template: {COLUMN}.
     */
    public static function resolveForAsc(string $name): string
    {
        return Str::of($name)
            ->snake()
            ->upper()
            ->__toString();
    }

    /**
     * Build the enum case for the desc direction.
     * Template: {COLUMN}_DESC.
     */
    public static function resolveForDesc(string $name): string
    {
        return Str::of($name)
            ->snake()
            ->upper()
            ->append('_DESC')
            ->__toString();
    }
}
