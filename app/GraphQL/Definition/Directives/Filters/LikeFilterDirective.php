<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use Illuminate\Support\Str;

/**
 * Class LikeFilterDirective.
 */
class LikeFilterDirective extends FilterDirective
{
    /**
     * Create the argument for the directive.
     *
     * @return string
     */
    public function toString(): string
    {
        return Str::of($this->field->getName().'_like')
            ->append(': ')
            ->append($this->type->toString())
            ->append(" @where(operator: \"like\", key: \"{$this->field->getColumn()}\")")
            ->toString();
    }
}
