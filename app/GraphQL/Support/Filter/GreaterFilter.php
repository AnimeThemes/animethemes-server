<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Criteria\Filter\WhereFilterCriteria;
use App\GraphQL\Support\Argument\Argument;

class GreaterFilter extends Filter
{
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_greater', $this->field->baseType())
            ->withDefaultValue($this->defaultValue);
    }

    public function criteria(mixed $value): WhereFilterCriteria
    {
        return new WhereFilterCriteria(
            $this,
            ComparisonOperator::GT,
            $value
        );
    }
}
