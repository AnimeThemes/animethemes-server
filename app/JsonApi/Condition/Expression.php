<?php

namespace App\JsonApi\Condition;

class Expression
{
    /**
     * The expression value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create a new expression.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get expression value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
