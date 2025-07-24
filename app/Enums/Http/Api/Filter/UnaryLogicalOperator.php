<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

use App\Concerns\Enums\CoercesInstances;

enum UnaryLogicalOperator: string
{
    use CoercesInstances;

    case NOT = 'not';
}
