<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use Illuminate\Support\Str;

class LesserFilterDirective extends FilterDirective
{
    /**
     * Create the argument for the directive.
     */
    public function __toString(): string
    {
        return Str::of($this->field->getName().'_lesser')
            ->append(': ')
            ->append($this->type->__toString())
            ->append($this->resolveDirectives([
                'where' => [
                    'operator' => ComparisonOperator::LT->value,
                    'key' => $this->field->getColumn(),
                ],
            ]))
            ->__toString();
    }
}
