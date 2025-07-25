<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

use App\Enums\GraphQL\SortType;

interface SortableField
{
    /**
     * The sort type of the field.
     */
    public function sortType(): SortType;
}
