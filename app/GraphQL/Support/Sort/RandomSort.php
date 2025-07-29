<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Sort;

use App\Enums\GraphQL\SortDirection;

class RandomSort extends Sort
{
    final public const CASE = 'RANDOM';

    public function __construct()
    {
        parent::__construct('random', SortDirection::ASC);
    }
}
