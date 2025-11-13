<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Criteria\Filter\WhereFilterCriteria;
use App\GraphQL\Support\Argument\Argument;

class LikeFilter extends Filter
{
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_like', $this->field->baseType())
            ->withDefaultValue($this->defaultValue);
    }

    public function criteria(mixed $value): WhereFilterCriteria
    {
        return new WhereFilterCriteria(
            $this,
            ComparisonOperator::LIKE,
            $value
        );
    }
}
