<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Enums;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortDirection;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Criteria\Sort\AggregateSortCriteria;
use App\GraphQL\Criteria\Sort\CountSortCriteria;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\PivotSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\RelationSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Relations\BelongsToRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\EnumType;

/**
 * Dynamic enum type to build the {Type}SortableColumns enums.
 */
class SortableColumns extends EnumType
{
    final public const string SUFFIX = 'SortableColumns';

    public function __construct(
        protected BaseType $type,
        protected ?PivotType $pivotType = null,
        protected ?BelongsToMany $relation = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        $name = $this->pivotType instanceof PivotType
            ? $this->pivotType->getName()
            : $this->type->getName();

        return [
            'name' => $name.self::SUFFIX,
            'values' => $this->getCriteria()->mapWithKeys(fn (SortCriteria $criterion): array => [$criterion->__toString() => $criterion->__toString()])->all(),
            'criteria' => $this->getCriteria()->mapWithKeys(fn (SortCriteria $criterion): array => [$criterion->__toString() => $criterion])->all(),
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

    private function getCriteria(): Collection
    {
        return $this->getFieldSortCriteria()
            ->merge($this->getPivotSortCriteria())
            ->merge($this->getCountSortCriteria())
            ->merge($this->getAggregateSortCriteria())
            ->merge($this->getRelationSortCriteria())
            ->push(new RandomSortCriteria());
    }

    private function getFieldSortCriteria(): Collection
    {
        return $this->getSortableFields()
            ->filter(fn (Field $field): bool => $field->sortType() === SortType::ROOT)
            ->map(fn (Field $field): array => [
                new FieldSortCriteria($field),
                new FieldSortCriteria($field, SortDirection::DESC),
            ])
            ->flatten();
    }

    private function getPivotSortCriteria(): Collection
    {
        return collect($this->pivotType?->fieldClasses() ?? [])
            ->filter(fn (Field $field): bool => $field instanceof SortableField)
            ->map(fn (Field $field): array => [
                new PivotSortCriteria($field, SortDirection::ASC, $this->relation),
                new PivotSortCriteria($field, SortDirection::DESC, $this->relation),
            ])
            ->flatten();
    }

    private function getCountSortCriteria(): Collection
    {
        return $this->getSortableFields()
            ->filter(fn (Field $field): bool => $field->sortType() === SortType::COUNT_RELATION)
            ->map(fn (Field $field): array => [
                new CountSortCriteria($field),
                new CountSortCriteria($field, SortDirection::DESC),
            ])
            ->flatten();
    }

    private function getAggregateSortCriteria(): Collection
    {
        return $this->getSortableFields()
            ->filter(fn (Field $field): bool => $field->sortType() === SortType::AGGREGATE)
            ->map(fn (Field $field): array => [
                new AggregateSortCriteria($field),
                new AggregateSortCriteria($field, SortDirection::DESC),
            ])
            ->flatten();
    }

    private function getRelationSortCriteria(): Collection
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
