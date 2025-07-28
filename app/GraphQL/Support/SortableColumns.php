<?php

declare(strict_types=1);

namespace App\GraphQL\Support;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Contracts\GraphQL\HasFields;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\BaseType;
use Stringable;

final readonly class SortableColumns implements Stringable
{
    final public const RANDOM = 'RANDOM';
    final public const SUFFIX = 'SortableColumns';

    public function __construct(protected BaseType&HasFields $type) {}

    /**
     * Resolve the enum cases.
     */
    protected function resolveEnumCases(): string
    {
        return collect($this->type->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (Field $field) => [SortDirection::resolveForAsc($field), SortDirection::resolveForDesc($field)])
            ->flatten()
            ->push(self::RANDOM)
            ->implode(PHP_EOL);
    }

    /**
     * Resolve the SortableColumns as a GraphQL string representation.
     */
    public function __toString(): string
    {
        $enumCases = $this->resolveEnumCases();

        if (blank($enumCases) || (method_exists($this->type, 'sortable') && ! $this->type->{'sortable'}())) {
            return '';
        }

        return sprintf(
            'enum %s%s {
                %s
            }',
            $this->type->getName(),
            self::SUFFIX,
            $enumCases,
        );
    }
}
