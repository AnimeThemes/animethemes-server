<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\Sort\SortDirection;
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
     * Template: {RELATION}_{FIELD_NAME}.
     * Template: {RELATION}_{FIELD_NAME}_DESC.
     */
    public function __toString(): string
    {
        $name = Str::of($this->relation)
            ->append('_')
            ->append($this->field->getName())
            ->snake()
            ->upper();

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

        return $builder->orderBy("{$this->relation}_$column", $this->direction->value);
    }
}
