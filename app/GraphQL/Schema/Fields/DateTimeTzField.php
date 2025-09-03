<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\GraphQL\Support\Filter\EqFilter;
use App\GraphQL\Support\Filter\Filter;
use App\GraphQL\Support\Filter\GreaterFilter;
use App\GraphQL\Support\Filter\LesserFilter;

abstract class DateTimeTzField extends StringField
{
    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return [
            new EqFilter($this),
            new LesserFilter($this),
            new GreaterFilter($this),
        ];
    }
}
