<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\GraphQL\Argument\Argument;
use App\GraphQL\Criteria\Filter\FilterCriteria;

abstract class Filter
{
    abstract public function argument(): Argument;

    abstract public function criteria(mixed $value): FilterCriteria;
}
