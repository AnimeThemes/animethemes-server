<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use Illuminate\Support\Str;

class LikeFilterDirective extends FilterDirective
{
    /**
     * Create the argument for the directive.
     *
     * @return string
     */
    public function __toString(): string
    {
        return Str::of($this->field->getName().'_like')
            ->append(': ')
            ->append($this->type->__toString())
            ->append($this->resolveDirectives([
                'where' => [
                    'operator' => ComparisonOperator::LIKE->value,
                    'key' => $this->field->getColumn(),
                ],
            ]))
            ->__toString();
    }
}
