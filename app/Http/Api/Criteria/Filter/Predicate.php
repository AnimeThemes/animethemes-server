<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;

/**
 * Class Predicate.
 */
class Predicate
{
    /**
     * Create a new predicate.
     *
     * @param  string  $column
     * @param  ComparisonOperator|null  $operator
     * @param  Expression  $expression
     */
    public function __construct(
        protected string $column,
        protected ?ComparisonOperator $operator,
        protected Expression $expression
    ) {}

    /**
     * Get the predicate column.
     *
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Get the predicate operator.
     *
     * @return ComparisonOperator|null
     */
    public function getOperator(): ?ComparisonOperator
    {
        return $this->operator;
    }

    /**
     * Get the predicate expression.
     *
     * @return Expression
     */
    public function getExpression(): Expression
    {
        return $this->expression;
    }
}
