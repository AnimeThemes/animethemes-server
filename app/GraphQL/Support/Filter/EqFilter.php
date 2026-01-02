<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Criteria\Filter\WhereFilterCriteria;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Support\Argument\Argument;

class EqFilter extends Filter
{
    public function __construct(
        protected Field $field,
        protected mixed $defaultValue = null,
    ) {}

    public function argument(): Argument
    {
        return new Argument($this->field->getName(), $this->field->baseType())
            ->withDefaultValue($this->defaultValue);
    }

    public function criteria(mixed $value): WhereFilterCriteria
    {
        return new WhereFilterCriteria(
            $this->field,
            ComparisonOperator::EQ,
            $value
        );
    }
}
