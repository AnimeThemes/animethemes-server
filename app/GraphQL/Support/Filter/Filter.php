<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\GraphQL\Criteria\Filter\FilterCriteria;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Support\Argument\Argument;

abstract class Filter
{
    public function __construct(
        protected Field $field,
        protected mixed $defaultValue = null,
    ) {}

    public function getColumn(): string
    {
        return $this->field->getColumn();
    }

    abstract public function argument(): Argument;

    abstract public function criteria(mixed $value): FilterCriteria;
}
