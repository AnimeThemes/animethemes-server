<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Filter\Filter;

interface FilterableField
{
    /**
     * Get the filters that can be applied to the field.
     */
    public function getFilter(): Filter;
}
