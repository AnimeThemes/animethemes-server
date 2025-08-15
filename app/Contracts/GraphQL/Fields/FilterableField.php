<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

use App\GraphQL\Support\Filter\Filter;

interface FilterableField
{
    /**
     * The filters of the field.
     *
     * @return Filter[]
     */
    public function getFilters(): array;
}
