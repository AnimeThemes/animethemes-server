<?php

declare(strict_types=1);

namespace App\GraphQL\Support;

use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Sort\Sort;
use Stringable;

final readonly class SortableColumns implements Stringable
{
    final public const SUFFIX = 'SortableColumns';

    public function __construct(protected BaseType $type) {}

    /**
     * Resolve the enum cases.
     */
    protected function resolveEnumCases(): string
    {
        return $this->type->sorts()
            ->map(fn (Sort $sort) => $sort->__toString())
            ->flatten()
            ->implode(PHP_EOL);
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
            'enum %s%s {
                %s
            }',
            $this->type->getName(),
            self::SUFFIX,
            $enumCases,
        );
    }
}
