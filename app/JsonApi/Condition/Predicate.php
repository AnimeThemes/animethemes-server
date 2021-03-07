<?php

namespace App\JsonApi\Condition;

use App\Enums\Filter\ComparisonOperator;

class Predicate
{
    /**
     * The predicate column.
     *
     * @var string
     */
    protected $column;

    /**
     * The comparison operator of the predicate.
     *
     * @var ComparisonOperator|null
     */
    protected $operator;

    /**
     * The expression of the predicate.
     *
     * @var Expression
     */
    protected $expression;

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
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Get the predicate operator.
     *
     * @return ComparisonOperator|null $operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Get the predicate expression.
     *
     * @return Expression $expression
     */
    public function getExpression()
    {
        return $this->expression;
    }
}
