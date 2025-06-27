<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

/**
 * Class Expression.
 */
readonly class Expression
{
    /**
     * Create a new expression.
     *
     * @param  mixed  $value
     */
    public function __construct(protected mixed $value) {}

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
