<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Schema\Fields\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class RelationSortCriteria extends SortCriteria
{
    public function __construct(
        protected Field&SortableField $field,
        public string $relation,
        protected SortDirection $direction = SortDirection::ASC
    ) {
        parent::__construct($field, $direction);
    }

    /**
     * Build the enum case for a direction.
     * Template: {RELATION}_{COLUMN}.
     * Template: {RELATION}_{COLUMN}_DESC.
     */
    public function __toString(): string
    {
        $name = Str::of($this->relation.'_'.$this->field->getName())->snake()->upper();

        return (string) match ($this->direction) {
            SortDirection::ASC => $name,
            SortDirection::DESC => $name->append('_DESC'),
        };
    }

    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder): Builder
    {
        $column = $this->field->getColumn();

        $builder->withAggregate([
            "{$this->relation} as {$this->relation}_$column" => function ($query) use ($column): void {
                $query->orderBy($column, $this->direction->value);
            },
        ], $column);

        return $builder->orderBy("{$this->relation}_$column", $this->direction->value);
    }
}
