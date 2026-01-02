<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\GraphQL\Criteria\Filter\FilterCriteria;
use App\GraphQL\Support\Argument\Argument;

abstract class Filter
{
    abstract public function argument(): Argument;

    abstract public function criteria(mixed $value): FilterCriteria;
}
