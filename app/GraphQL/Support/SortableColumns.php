<?php

declare(strict_types=1);

namespace App\GraphQL\Support;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Sort\RandomSort;
use App\GraphQL\Support\Sort\Sort;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\EnumType;

/**
 * Dynamic enum type to build the {Type}SortableColumns enums.
 */
class SortableColumns extends EnumType
{
    final public const SUFFIX = 'SortableColumns';

    final public const RESOLVER_COLUMN = 'column';
    final public const RESOLVER_SORT_TYPE = 'sortType';
    final public const RESOLVER_RELATION = 'relation';

    public function __construct(protected BaseType $type) {}

    /**
     * Get the attributes of the type.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->type->getName().self::SUFFIX,
            'values' => $this->getValues(),
        ];
    }

    /**
     * Get the resolvers for every value.
     *
     * @return array<string, array>
     */
    public function getValuesWithResolver(): array
    {
        return $this->getSortableFields()
            ->mapWithKeys(function (Field&SortableField $field) {
                return [
                    Str::of($field->getName())->snake()->upper()->__toString() => [
                        self::RESOLVER_COLUMN => $field->getColumn(),
                        self::RESOLVER_SORT_TYPE => $field->sortType(),
                        self::RESOLVER_RELATION => method_exists($field, 'relation') ? $field->{'relation'}() : null,
                    ],
                ];
            })
            /** @phpstan-ignore-next-line */
            ->push([RandomSort::CASE => []])
            ->toArray();
    }

    /**
     * Get the sortable fields.
     *
     * @return Collection<int, Field&SortableField>
     */
    private function getSortableFields(): Collection
    {
        return collect($this->type->fieldClasses())
            ->filter(fn (Field $field) => $field instanceof SortableField);
    }

    /**
     * Get the values of the enum.
     *
     * @return array<string, string>
     */
    private function getValues(): array
    {
        return $this->getSortableFields()
            ->map(fn (Field $field) => [new Sort($field->getName()), new Sort($field->getName(), SortDirection::DESC)])
            ->flatten()
            ->push(new RandomSort())
            ->mapWithKeys(fn (Sort $sort) => [$sort->__toString() => $sort->__toString()])
            ->toArray();
    }
}
