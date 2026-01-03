<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Criteria\Filter\WhereFilterCriteria;
use App\GraphQL\Schema\Fields\Field;

class LesserFilter extends Filter
{
    public function __construct(
        protected Field $field,
        protected mixed $defaultValue = null,
    ) {}

    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_lesser', $this->field->baseType())
            ->withDefaultValue($this->defaultValue);
    }

    public function criteria(mixed $value): WhereFilterCriteria
    {
        return new WhereFilterCriteria(
            $this->field,
            ComparisonOperator::LT,
            $value
        );
    }
}
