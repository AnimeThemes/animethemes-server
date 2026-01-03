<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\GraphQL\Argument\Argument;
use App\GraphQL\Criteria\Filter\WhereInFilterCriteria;
use App\GraphQL\Schema\Fields\Field;
use GraphQL\Type\Definition\Type;

class NotInFilter extends Filter
{
    public function __construct(
        protected Field $field,
        protected mixed $defaultValue = null,
    ) {}

    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_not_in', Type::listOf(Type::nonNull($this->field->baseType())))
            ->withDefaultValue($this->defaultValue);
    }

    public function criteria(mixed $value): WhereInFilterCriteria
    {
        return new WhereInFilterCriteria(
            $this->field,
            $value,
            true
        );
    }
}
