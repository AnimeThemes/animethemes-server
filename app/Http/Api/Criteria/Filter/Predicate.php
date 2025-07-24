<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;

readonly class Predicate
{
    public function __construct(
        protected string $column,
        protected ?ComparisonOperator $operator,
        protected Expression $expression
    ) {}

    /**
     * Get the predicate column.
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Get the predicate operator.
     */
    public function getOperator(): ?ComparisonOperator
    {
        return $this->operator;
    }

    /**
     * Get the predicate expression.
     */
    public function getExpression(): Expression
    {
        return $this->expression;
    }
}
