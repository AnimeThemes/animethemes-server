<?php

namespace App\JsonApi;

use Illuminate\Support\Arr;
use Neomerx\JsonApi\Representation\FieldSetFilter as BaseFieldSetFilter;

class FieldSetFilter extends BaseFieldSetFilter
{
    /**
     * Determine if field should be included in the response for this type
     *
     * @param string $type
     * @param string $field
     *
     * @return bool
     */
    public function isAllowedField($type, $field)
    {
        // If we aren't filtering this type, include all fields
        if (!$this->hasFilter($type)) {
            return true;
        }

        // If there are no allowed fields for this type, include all fields
        $allowedFields = $this->getAllowedFields($type);
        if (empty($allowedFields)) {
            return true;
        }

        // Is field included for this type
        return Arr::has($allowedFields, $field);
    }
}
