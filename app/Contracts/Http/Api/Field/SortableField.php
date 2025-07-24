<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Sort\Sort;

interface SortableField
{
    /**
     * Get the sort that can be applied to the field.
     */
    public function getSort(): Sort;
}
