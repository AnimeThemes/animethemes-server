<?php

declare(strict_types=1);

namespace App\GraphQL\Sort;

use App\Enums\GraphQL\QualifyColumn;
use App\Enums\GraphQL\SortDirection;
use App\Enums\Http\Api\Field\AggregateFunction;
use Illuminate\Database\Eloquent\Builder;
use Stringable;

abstract class SortCriteria implements Stringable
{
    public ?string $aggregateRelation = null;
    public ?AggregateFunction $function = null;

    public function __construct(
        protected string $enumName,
        protected string $column,
        protected SortDirection $direction = SortDirection::ASC,
        protected bool $isStringField = false,
        protected QualifyColumn $qualifyColumn = QualifyColumn::YES,
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

    public function setAggregateRelation(string $relation, AggregateFunction $function): static
    {
        $this->aggregateRelation = $relation;

        $this->function = $function;

        return $this;
    }

    /**
     * Build the enum case for a direction.
     * Template: {FIELD_NAME}.
     * Template: {FIELD_NAME}_DESC.
     */
    public function __toString(): string
    {
        return $this->enumName;
    }

    /**
     * Apply the ordering to the current Eloquent builder.
     */
    abstract public function sort(Builder $builder): Builder;
}
