<?php

declare(strict_types=1);

namespace App\Enums\Http\Api\Filter;

use App\Concerns\Enums\CoercesInstances;

/**
 * Enum BinaryLogicalOperator.
 */
enum BinaryLogicalOperator: string
{
    use CoercesInstances;

    case AND = 'and';
    case OR = 'or';
}
