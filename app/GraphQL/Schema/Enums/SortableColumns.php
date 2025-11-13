<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Enums;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortDirection;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Criteria\Sort\AggregateSortCriteria;
use App\GraphQL\Criteria\Sort\CountSortCriteria;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\RelationSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\EnumType;

/**
 * Dynamic enum type to build the {Type}SortableColumns enums.
 */
class SortableColumns extends EnumType
{
    final public const string SUFFIX = 'SortableColumns';

    public function __construct(protected BaseType $type) {}

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->type->getName().self::SUFFIX,
            'values' => $this->getCriterias()->mapWithKeys(fn (SortCriteria $sort): array => [$sort->__toString() => $sort->__toString()])->toArray(),
            'criterias' => $this->getCriterias()->mapWithKeys(fn (SortCriteria $sort): array => [$sort->__toString() => $sort])->toArray(),
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

    private function getCriterias(): Collection
    {
        return $this->getFieldSortCriterias()
            ->merge($this->getCountSortCriterias())
            ->merge($this->getAggregateSortCriterias())
            ->merge($this->getRelationSortCriterias())
            ->push(new RandomSortCriteria());
    }

    private function getFieldSortCriterias(): Collection
    {
        return $this->getSortableFields()
            ->filter(fn (Field $field): bool => $field->sortType() === SortType::ROOT)
            ->map(fn (Field $field): array => [new FieldSortCriteria($field), new FieldSortCriteria($field, SortDirection::DESC)])
            ->flatten();
    }

    private function getCountSortCriterias(): Collection
    {
        return $this->getSortableFields()
            ->filter(fn (Field $field): bool => $field->sortType() === SortType::COUNT_RELATION)
            ->map(fn (Field $field): array => [new CountSortCriteria($field), new CountSortCriteria($field, SortDirection::DESC)])
            ->flatten();
    }

    private function getAggregateSortCriterias(): Collection
    {
        return $this->getSortableFields()
            ->filter(fn (Field $field): bool => $field->sortType() === SortType::AGGREGATE)
            ->map(fn (Field $field): array => [new AggregateSortCriteria($field), new AggregateSortCriteria($field, SortDirection::DESC)])
            ->flatten();
    }

    private function getRelationSortCriterias(): Collection
    {
        return $this->getRelations($this->type)
            ->flatMap(fn (Collection $fields, $relation) => $fields->map(fn (Field&SortableField $field): array => [
                new RelationSortCriteria($field, $relation),
                new RelationSortCriteria($field, $relation, SortDirection::DESC),
            ]))
            ->flatten();
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
}
