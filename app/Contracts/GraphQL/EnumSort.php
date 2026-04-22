<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

use App\GraphQL\Sort\SortCriteria;

interface EnumSort
{
    public function getSortCriteria(): SortCriteria;
}
