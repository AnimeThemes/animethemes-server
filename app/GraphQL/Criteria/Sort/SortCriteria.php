<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\GraphQL\SortDirection;
use Illuminate\Database\Eloquent\Builder;
use Stringable;
use UnitEnum;

abstract class SortCriteria implements Stringable
{
    public function __construct(
        protected UnitEnum&EnumSort $sortCase,
        protected string $column,
        protected SortDirection $direction = SortDirection::ASC,
        protected bool $isStringField = false,
    ) {}

    public function getColumn(): string
    {
        return $this->column;
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
        return $this->sortCase->name;
    }

    /**
     * Apply the ordering to the current Eloquent builder.
     */
    abstract public function sort(Builder $builder): Builder;
}
