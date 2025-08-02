<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Sort;

use App\Enums\GraphQL\SortDirection;
use Illuminate\Support\Str;
use Stringable;

class Sort implements Stringable
{
    public function __construct(
        protected string $name,
        protected SortDirection $direction = SortDirection::ASC,
    ) {}

    /**
     * Build the enum case for a direction.
     * Template: {COLUMN}.
     * Template: {COLUMN}_DESC.
     */
    public function __toString(): string
    {
        return (string) match ($this->direction) {
            SortDirection::ASC => Str::of($this->name)->snake()->upper(),
            SortDirection::DESC => Str::of($this->name)->snake()->upper()->append('_DESC'),
        };
    }

    /**
     * Apply the reverse engine.
     */
    public static function resolveFromEnumCase(string $enumCase): string
    {
        return Str::endsWith($enumCase, '_DESC')
            ? SortDirection::DESC->value
            : SortDirection::ASC->value;
    }
}
