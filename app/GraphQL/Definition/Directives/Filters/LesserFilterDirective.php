<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use Illuminate\Support\Str;

/**
 * Class LesserFilterDirective.
 */
class LesserFilterDirective extends FilterDirective
{
    /**
     * Create the argument for the directive.
     *
     * @return string
     */
    public function toString(): string
    {
        return Str::of($this->field->getName().'_lesser')
            ->append(': ')
            ->append($this->type->toString())
            ->append(" @where(operator: \"<\", key: \"{$this->field->getColumn()}\")")
            ->toString();
    }
}
