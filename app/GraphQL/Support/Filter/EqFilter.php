<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Support\Argument\Argument;
use Illuminate\Database\Eloquent\Builder;

class EqFilter extends Filter
{
    /**
     * Get the argument to apply the filter.
     */
    public function argument(): Argument
    {
        return new Argument($this->field->getName(), $this->field->type())
            ->withDefaultValue($this->defaultValue);
    }

    /**
     * Apply the filter to the builder.
     */
    public function apply(Builder $builder, mixed $value): Builder
    {
        return $builder->where(
            $this->field->getColumn(),
            ComparisonOperator::EQ->value,
            $value,
        );
    }
}
