<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\StringFilter;

/**
 * Class StringField.
 */
class StringField extends Field
{
    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new StringFilter($this->getKey(), $this->getColumn());
    }
}
