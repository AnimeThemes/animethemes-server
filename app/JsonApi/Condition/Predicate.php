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
     * @var \App\Enums\Filter\ComparisonOperator|null
     */
    protected $operator;

    /**
     * The expression of the predicate.
     *
     * @var \App\JsonApi\Condition\Expression
     */
    protected $expression;

    /**
     * Create a new predicate.
     *
     * @param string $column
     * @param \App\Enums\Filter\ComparisonOperator|null $operator
     * @param \App\JsonApi\Condition\Expression $expression
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
     * @return \App\Enums\Filter\ComparisonOperator|null $operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Get the predicate expression.
     *
     * @return \App\JsonApi\Condition\Expression $expression
     */
    public function getExpression()
    {
        return $this->expression;
    }
}
