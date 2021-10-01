<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\Filter\Filter;

/**
 * Class BooleanField.
 */
class BooleanField extends Field
{
    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new BooleanFilter($this->getKey(), $this->getColumn());
    }
}
