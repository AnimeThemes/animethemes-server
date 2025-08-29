<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

use App\GraphQL\Support\Filter\Filter;

interface FilterableField
{
    /**
     * @return Filter[]
     */
    public function getFilters(): array;
}
