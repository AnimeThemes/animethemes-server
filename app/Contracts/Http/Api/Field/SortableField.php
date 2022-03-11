<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Sort\Sort;

/**
 * Interface SortableColumn.
 */
interface SortableField
{
    /**
     * Get the sort that can be applied to the field.
     *
     * @return Sort
     */
    public function getSort(): Sort;
}
