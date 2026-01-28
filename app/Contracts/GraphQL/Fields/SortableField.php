<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

use App\GraphQL\Sort\Sort;

interface SortableField
{
    public function getSort(): Sort;
}
