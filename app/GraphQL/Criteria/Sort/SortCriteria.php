<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Schema\Fields\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Stringable;

abstract class SortCriteria implements Stringable
{
    public function __construct(
        protected Field&SortableField $field,
        protected SortDirection $direction = SortDirection::ASC
    ) {}

    public function getField(): Field&SortableField
    {
        return $this->field;
    }

    public function getDirection(): SortDirection
    {
        return $this->direction;
    }

    /**
     * Build the enum case for a direction.
     * Template: {COLUMN}.
     * Template: {COLUMN}_DESC.
     */
    public function __toString(): string
    {
        return (string) match ($this->direction) {
            SortDirection::ASC => Str::of($this->field->getName())->snake()->upper(),
            SortDirection::DESC => Str::of($this->field->getName())->snake()->upper()->append('_DESC'),
        };
    }

    /**
     * Apply the ordering to the current Eloquent builder.
     */
    abstract public function sort(Builder $builder): Builder;
}
