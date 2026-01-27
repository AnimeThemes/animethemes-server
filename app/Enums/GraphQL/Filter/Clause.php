<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter;

enum Clause
{
    case WHERE;
    case HAVING;
}
