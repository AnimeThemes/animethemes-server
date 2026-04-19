<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

use App\GraphQL\Criteria\Sort\SortCriteria;

interface EnumSort
{
    public function getSortCriteria(): SortCriteria;

    public function shouldQualifyColumn(): bool;
}
