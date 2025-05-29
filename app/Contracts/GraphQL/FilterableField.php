<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

use App\GraphQL\Definition\Filters\Filter;

/**
 * Interface FilterableField.
 */
interface FilterableField
{
    /**
     * Get the filter for this field.
     *
     * @return Filter
     */
    public function getFilter(): Filter;
}
