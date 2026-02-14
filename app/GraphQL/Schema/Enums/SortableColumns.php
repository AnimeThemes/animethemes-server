<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Enums;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\Sort\SortDirection;
use App\GraphQL\Criteria\Sort\FieldSortCriteria;
use App\GraphQL\Criteria\Sort\PivotSortCriteria;
use App\GraphQL\Criteria\Sort\RandomSortCriteria;
use App\GraphQL\Criteria\Sort\RelationSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Fields\Relations\Relation;
use App\GraphQL\Schema\Fields\StringField;
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
        $typeName = $this->type->getName();

        $name = $this->pivotType instanceof PivotType
            ? $typeName.$this->pivotType->getName()
            : $typeName;

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
            ->merge($this->getRelationSortCriteria())
            ->push(new RandomSortCriteria());
    }

    private function getFieldSortCriteria(): Collection
    {
        return $this->getSortableFields()
            ->map(fn (Field&SortableField $field): array => [
                new FieldSortCriteria($field->getSort(), SortDirection::ASC, $field instanceof StringField),
                new FieldSortCriteria($field->getSort(), SortDirection::DESC, $field instanceof StringField),
            ])
            ->flatten();
    }

    private function getPivotSortCriteria(): Collection
    {
        return collect($this->pivotType?->fieldClasses() ?? [])
            ->filter(fn (Field $field): bool => $field instanceof SortableField)
            ->map(fn (Field&SortableField $field): array => [
                new PivotSortCriteria($field->getSort(), SortDirection::ASC, $this->relation, $field instanceof StringField),
                new PivotSortCriteria($field->getSort(), SortDirection::DESC, $this->relation, $field instanceof StringField),
            ])
            ->flatten();
    }

    private function getRelationSortCriteria(): Collection
    {
        return $this->getRelations($this->type)
            ->flatMap(fn (Collection $fields, $relation) => $fields->map(fn (Field&SortableField $field): array => [
                new RelationSortCriteria($field->getSort(), $relation, SortDirection::ASC, $field instanceof StringField),
                new RelationSortCriteria($field->getSort(), $relation, SortDirection::DESC, $field instanceof StringField),
            ]))
            ->flatten();
    }

    private function getRelations(BaseType $type): Collection
    {
        return collect($type->relations())
            ->filter(fn (Relation $relation): bool => $relation instanceof BelongsToRelation && $relation->baseType() instanceof BaseType)
            ->mapWithKeys(
                fn (Relation $relation): array => [
                    $relation->getName() => collect($relation->baseType()->fieldClasses())
                        ->filter(fn (Field $field): bool => $field instanceof SortableField),
                ],
            );
    }
}
