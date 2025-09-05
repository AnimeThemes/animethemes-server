<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Support\Argument\Argument;
use Illuminate\Database\Eloquent\Builder;

class GreaterFilter extends Filter
{
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_greater', $this->field->baseType())
            ->withDefaultValue($this->defaultValue);
    }

    public function filter(Builder $builder, mixed $value): Builder
    {
        return $builder->where(
            $this->field->getColumn(),
            ComparisonOperator::GT->value,
            $value,
        );
    }
}
