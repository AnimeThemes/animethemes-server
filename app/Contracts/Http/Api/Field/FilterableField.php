<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Filter\Filter;

interface FilterableField
{
    public function getFilter(): Filter;
}
