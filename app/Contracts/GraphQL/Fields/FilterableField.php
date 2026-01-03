<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

use App\GraphQL\Filter\Filter;

interface FilterableField
{
    public function getFilter(): Filter;
}
