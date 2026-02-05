<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use App\Enums\GraphQL\Sort\SortDirection;
use App\GraphQL\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PivotSortCriteria extends SortCriteria
{
    public function __construct(
        protected Sort $sort,
        protected SortDirection $direction = SortDirection::ASC,
        protected ?BelongsToMany $relation = null,
        protected bool $isStringField = false,
    ) {
        parent::__construct($sort, $direction, $isStringField);
    }

    /**
     * Build the enum case for a direction.
     * Template: PIVOT_{FIELD_NAME}.
     * Template: PIVOT_{FIELD_NAME}_DESC.
     */
    public function __toString(): string
    {
        $name = Str::of($this->getSort()->getName())
            ->snake()
            ->upper()
            ->prepend('PIVOT_');

        return (string) match ($this->getDirection()) {
            SortDirection::ASC => $name,
            SortDirection::DESC => $name->append('_DESC'),
        };
    }

    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder): Builder
    {
        $sort = $this->getSort();

        $column = $sort->shouldQualifyColumn()
            ? $this->relation?->qualifyPivotColumn($sort->getColumn())
            : $sort->getColumn();

        return $builder->orderBy($column, $this->getDirection()->value);
    }
}
