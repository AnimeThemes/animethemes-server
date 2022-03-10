<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Filter\Filter;

/**
 * Interface FilterableColumn.
 */
interface FilterableField
{
    /**
     * Get the filters that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter;
}
