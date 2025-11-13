<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Enums;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortDirection;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
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
    final public const string SUFFIX = 'SortableColumns';

    final public const string RESOLVER_FIELD = 'field';
    final public const string RESOLVER_COLUMN = 'column';
    final public const string RESOLVER_SORT_TYPE = 'sortType';
    final public const string RESOLVER_RELATION = 'relation';

    public function __construct(protected BaseType $type) {}

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->type->getName().self::SUFFIX,
            'values' => $this->getValues(),
            'resolvers' => $this->getValuesWithResolver(),
        ];
    }

    /**
     * @return Collection<int, Field&SortableField>
     */
    private function getSortableFields(): Collection
    {
        return collect($this->type->fieldClasses())
            ->filter(fn (Field $field): bool => $field instanceof SortableField);
    }

    private function getRelations(BaseType $type): Collection
    {
        return collect($type->relations())
            ->filter(fn (Relation $relation): bool => $relation instanceof BelongsToRelation && $relation->getBaseType() instanceof BaseType)
            ->mapWithKeys(
                fn (Relation $relation): array => [
                    $relation->getName() => collect($relation->getBaseType()->fieldClasses())
                        ->filter(fn (Field $field): bool => $field instanceof SortableField),
                ],
            );
    }

    /**
     * Get the values of the enum.
     *
     * @return array<string, string>
     */
    private function getValues(): array
    {
        return $this->getSortableFields()
            ->map(fn (Field $field): array => [new Sort($field->getName()), new Sort($field->getName(), SortDirection::DESC)])
            ->flatten()
            ->merge(
                $this->getRelations($this->type)
                    ->flatMap(fn (Collection $fields, $relation) => $fields->map(fn (Field $field): array => [
                        new Sort($relation.'_'.$field->getName()),
                        new Sort($relation.'_'.$field->getName(), SortDirection::DESC),
                    ]))
                    ->flatten()
            )
            ->push(new RandomSort())
            ->mapWithKeys(fn (Sort $sort): array => [$sort->__toString() => $sort->__toString()])
            ->toArray();
    }

    /**
     * Get the resolvers for every value.
     *
     * @return array<string, array>
     */
    private function getValuesWithResolver(): array
    {
        return $this->getSortableFields()
            ->mapWithKeys(fn (Field&SortableField $field): array => [
                Str::of($field->getName())->snake()->upper()->__toString() => [
                    self::RESOLVER_FIELD => $field,
                    self::RESOLVER_COLUMN => $field->getColumn(),
                    self::RESOLVER_SORT_TYPE => $field->sortType(),
                    self::RESOLVER_RELATION => method_exists($field, 'relation') ? $field->{'relation'}() : null,
                ],
            ])
            ->merge(
                $this->getRelations($this->type)
                    ->flatMap(fn (Collection $collection, $relation) => $collection->flatMap(fn (Field&SortableField $field): array => [
                        Str::upper($relation).'_'.Str::of($field->getName())->snake()->upper()->__toString() => [
                            self::RESOLVER_FIELD => $field,
                            self::RESOLVER_COLUMN => $field->getColumn(),
                            self::RESOLVER_SORT_TYPE => SortType::RELATION,
                            self::RESOLVER_RELATION => $relation,
                        ],
                    ]))
            )
            /** @phpstan-ignore-next-line */
            ->push([RandomSort::CASE => []])
            ->toArray();
    }
}
