<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;

class InFilterDirective extends FilterDirective
{
    /**
     * Create the argument for the directive.
     */
    public function __toString(): string
    {
        return Str::of($this->field->getName().'_in')
            ->append(': ')
            ->append(Type::listOf($this->type)->__toString())
            ->append(' ')
            ->append($this->resolveDirectives([
                'in' => [
                    'key' => $this->field->getColumn(),
                ],
            ]))
            ->__toString();
    }
}
