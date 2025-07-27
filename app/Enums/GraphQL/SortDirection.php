<?php

declare(strict_types=1);

namespace App\Enums\GraphQL;

use App\GraphQL\Definition\Fields\Field;
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

    /**
     * Build the enum case for the asc direction.
     * Template: {COLUMN}.
     */
    public static function resolveForAsc(Field $field): string
    {
        return Str::of($field->getName())
            ->snake()
            ->upper()
            ->__toString();
    }

    /**
     * Build the enum case for the desc direction.
     * Template: {COLUMN}_DESC.
     */
    public static function resolveForDesc(Field $field): string
    {
        return Str::of($field->getName())
            ->snake()
            ->upper()
            ->append('_DESC')
            ->__toString();
    }
}
