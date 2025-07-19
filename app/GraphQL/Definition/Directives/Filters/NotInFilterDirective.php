<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;

/**
 * Class NotInFilterDirective.
 */
class NotInFilterDirective extends FilterDirective
{
    /**
     * Create the argument for the directive.
     *
     * @return string
     */
    public function __toString(): string
    {
        return Str::of($this->field->getName().'_not_in')
            ->append(': ')
            ->append(Type::listOf($this->type)->__toString())
            ->append($this->resolveDirectives([
                'notIn' => [
                    'key' => $this->field->getColumn(),
                ],
            ]))
            ->__toString();
    }
}
