<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

readonly class Expression
{
    public function __construct(protected mixed $value) {}

    /**
     * Get expression value.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
