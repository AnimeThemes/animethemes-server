<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use Illuminate\Support\Str;

/**
 * Class EqFilterDirective.
 */
class EqFilterDirective extends FilterDirective
{
    /**
     * Create the argument for the directive.
     *
     * @return string
     */
    public function toString(): string
    {
        return Str::of($this->field->getName())
            ->append(': ')
            ->append($this->type->toString())
            ->append(" @eq(key: \"{$this->field->getColumn()}\")")
            ->toString();
    }
}
