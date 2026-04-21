<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

use App\GraphQL\Filter\Filter;

interface EnumFilterableColumns
{
    public function getFilter(): Filter;
}
