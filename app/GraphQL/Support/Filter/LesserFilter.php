<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Support\Argument\Argument;
use Illuminate\Database\Eloquent\Builder;

class LesserFilter extends Filter
{
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_lesser', $this->field->baseType())
            ->withDefaultValue($this->defaultValue);
    }

    public function filter(Builder $builder, mixed $value): Builder
    {
        return $builder->where(
            $builder->qualifyColumn($this->field->getColumn()),
            ComparisonOperator::LT->value,
            $value,
        );
    }
}
