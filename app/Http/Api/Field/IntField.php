<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\IntFilter;

/**
 * Class IntField.
 */
class IntField extends Field
{
    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new IntFilter($this->getKey(), $this->getColumn());
    }
}
