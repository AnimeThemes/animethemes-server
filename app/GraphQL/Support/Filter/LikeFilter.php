<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Support\Argument\Argument;
use Illuminate\Database\Eloquent\Builder;

class LikeFilter extends Filter
{
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_like', $this->field->baseType())
            ->withDefaultValue($this->defaultValue);
    }

    public function filter(Builder $builder, mixed $value): Builder
    {
        return $builder->where(
            $builder->qualifyColumn($this->field->getColumn()),
            ComparisonOperator::LIKE->value,
            $value,
        );
    }
}
