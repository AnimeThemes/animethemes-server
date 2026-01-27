<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\Sort\SortDirection;
use App\GraphQL\Schema\Fields\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PivotSortCriteria extends SortCriteria
{
    public function __construct(
        protected Field&SortableField $field,
        protected SortDirection $direction = SortDirection::ASC,
        protected ?BelongsToMany $relation = null,
    ) {
        parent::__construct($field, $direction);
    }

    /**
     * Build the enum case for a direction.
     * Template: PIVOT_{FIELD_NAME}.
     * Template: PIVOT_{FIELD_NAME}_DESC.
     */
    public function __toString(): string
    {
        $name = Str::of($this->field->getName())
            ->snake()
            ->upper()
            ->prepend('PIVOT_');

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
        $column = $this->field->getSort()->shouldQualifyColumn()
            ? $this->relation?->qualifyPivotColumn($this->field->getColumn())
            : $this->field->getColumn();

        return $builder->orderBy($column, $this->direction->value);
    }
}
