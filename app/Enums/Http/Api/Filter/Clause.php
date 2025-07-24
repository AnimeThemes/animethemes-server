<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

enum Clause
{
    case WHERE;
    case HAVING;
}
