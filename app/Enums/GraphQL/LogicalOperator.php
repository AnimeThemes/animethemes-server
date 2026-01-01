<?php

declare(strict_types=1);

namespace App\Enums\GraphQL;

use App\Concerns\Enums\CoercesInstances;

enum LogicalOperator: string
{
    use CoercesInstances;

    case AND = 'and';
    case OR = 'or';
}
