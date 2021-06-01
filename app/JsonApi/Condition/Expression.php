<?php

declare(strict_types=1);

namespace App\JsonApi\Condition;

/**
 * Class Expression
 * @package App\JsonApi\Condition
 */
class Expression
{
    /**
     * The expression value.
     *
     * @var mixed
     */
    protected mixed $value;

    /**
     * Create a new expression.
     *
     * @param mixed $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Get expression value.
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
