<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Directives\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Support\Argument;

final readonly class GreaterFilterDirective extends FilterDirective
{
    /**
     * Create the argument for the directive.
     */
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_greater', $this->type)
            ->directives([
                'where' => [
                    'operator' => ComparisonOperator::GT->value,
                    'key' => $this->field->getColumn(),
                ],
            ]);
    }
}
