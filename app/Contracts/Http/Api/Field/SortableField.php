<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Sort\Sort;

interface SortableField
{
    public function getSort(): Sort;
}
