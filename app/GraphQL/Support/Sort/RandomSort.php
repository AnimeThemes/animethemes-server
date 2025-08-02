<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Sort;

class RandomSort extends Sort
{
    final public const CASE = 'RANDOM';

    public function __construct()
    {
        parent::__construct('random');
    }
}
