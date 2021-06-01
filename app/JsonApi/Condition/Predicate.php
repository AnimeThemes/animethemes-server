<?php

declare(strict_types=1);

namespace App\JsonApi\Condition;

use App\Enums\Filter\ComparisonOperator;

/**
 * Class Predicate
 * @package App\JsonApi\Condition
 */
class Predicate
{
    /**
     * The predicate column.
     *
     * @var string
     */
    protected string $column;

    /**
     * The comparison operator of the predicate.
     *
     * @var ComparisonOperator|null
     */
    protected ?ComparisonOperator $operator;

    /**
     * The expression of the predicate.
     *
     * @var Expression
     */
    protected Expression $expression;

    /**
     * Create a new predicate.
     *
     * @param string $column
     * @param ComparisonOperator|null $operator
     * @param Expression $expression
     */
    public function __construct(
        string $column,
        ?ComparisonOperator $operator,
        Expression $expression
    ) {
        $this->column = $column;
        $this->operator = $operator;
        $this->expression = $expression;
    }

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
     * @return ComparisonOperator|null $operator
     */
    public function getOperator(): ?ComparisonOperator
    {
        return $this->operator;
    }

    /**
     * Get the predicate expression.
     *
     * @return Expression $expression
     */
    public function getExpression(): Expression
    {
        return $this->expression;
    }
}
