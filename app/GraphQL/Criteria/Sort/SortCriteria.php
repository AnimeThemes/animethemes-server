<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use App\Enums\GraphQL\Sort\SortDirection;
use App\GraphQL\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Stringable;

abstract class SortCriteria implements Stringable
{
    public function __construct(
        protected Sort $sort,
        protected SortDirection $direction = SortDirection::ASC,
        protected bool $isStringField = false,
    ) {}

    public function getSort(): Sort
    {
        return $this->sort;
    }

    public function getDirection(): SortDirection
    {
        return $this->direction;
    }

    public function isStringField(): bool
    {
        return $this->isStringField;
    }

    /**
     * Build the enum case for a direction.
     * Template: {FIELD_NAME}.
     * Template: {FIELD_NAME}_DESC.
     */
    public function __toString(): string
    {
        $name = Str::of($this->getSort()->getName())
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
    abstract public function sort(Builder $builder): Builder;
}
