<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Definition\Argument\Argument;

class GreaterFilterDirective extends FilterDirective
{
    /**
     * Create the argument for the directive.
     */
    public function argument(): Argument
    {
        return new Argument(
            $this->field->getName().'_greater',
            $this->type,
            [
                'where' => [
                    'operator' => ComparisonOperator::GT->value,
                    'key' => $this->field->getColumn(),
                ],
            ],
        );
    }
}
