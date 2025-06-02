<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Filters\Directives;

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
    public function toString(): string
    {
        return Str::of($this->field->getName().'_greater')
            ->append(': ')
            ->append($this->type->toString())
            ->append(" @where(operator: \">\", key: \"{$this->field->getColumn()}\")")
            ->toString();
    }
}
