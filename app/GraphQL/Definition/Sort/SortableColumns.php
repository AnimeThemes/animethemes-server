<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Sort;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Contracts\GraphQL\HasFields;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\BaseType;
use Illuminate\Support\Str;

class SortableColumns
{
    public function __construct(
        protected BaseType&HasFields $type,
    ) {}

    /**
     * Resolve the enum cases.
     */
    protected function resolveEnumCases(): string
    {
        return collect($this->type->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (Field $field) => [static::resolveForAsc($field), static::resolveForDesc($field)])
            ->flatten()
            ->implode(PHP_EOL);
    }

    /**
     * Build the enum case for the asc direction.
     * Template: {COLUMN}.
     */
    protected static function resolveForAsc(Field $field): string
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
    protected static function resolveForDesc(Field $field): string
    {
        return Str::of($field->getName())
            ->snake()
            ->upper()
            ->append('_DESC')
            ->__toString();
    }

    /**
     * Apply the reverse engine.
     */
    public static function resolveSortDirection(string $column): SortDirection
    {
        return Str::endsWith($column, '_DESC')
            ? SortDirection::DESC
            : SortDirection::ASC;
    }

    /**
     * Resolve the SortableColumns as a GraphQL string representation.
     */
    public function __toString(): string
    {
        $enumCases = $this->resolveEnumCases();

        if (blank($enumCases)) {
            return '';
        }

        return sprintf(
            'enum %sSortableColumns {
                %s
            }',
            $this->type->getName(),
            $enumCases,
        );
    }
}