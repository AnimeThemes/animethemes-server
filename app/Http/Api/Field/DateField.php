<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Http\Api\Filter\DateFilter;
use App\Http\Api\Filter\Filter;

/**
 * Class DateField.
 */
class DateField extends Field
{
    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new DateFilter($this->getKey(), $this->getColumn());
    }
}
