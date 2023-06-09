<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

use App\Concerns\Enums\CoercesInstances;

/**
 * Enum UnaryLogicalOperator.
 */
enum UnaryLogicalOperator: string
{
    use CoercesInstances;

    case NOT = 'not';
}
