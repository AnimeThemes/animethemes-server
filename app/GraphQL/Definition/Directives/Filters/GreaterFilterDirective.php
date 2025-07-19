<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use Illuminate\Support\Str;

/**
 * Class GreaterFilterDirective.
 */
class GreaterFilterDirective extends FilterDirective
{
    /**
     * Create the argument for the directive.
     *
     * @return string
     */
    public function __toString(): string
    {
        return Str::of($this->field->getName().'_greater')
            ->append(': ')
            ->append($this->type->__toString())
            ->append($this->resolveDirectives([
                'where' => [
                    'operator' => ComparisonOperator::GT->value,
                    'key' => $this->field->getColumn(),
                ],
            ]))
            ->__toString();
    }
}
