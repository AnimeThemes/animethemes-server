<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\GraphQL\Definition\Argument\Argument;
use GraphQL\Type\Definition\Type;

class InFilterDirective extends FilterDirective
{
    /**
     * The argument for the filter directive.
     */
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_in', Type::listOf($this->type))
            ->directives([
                'in' => [
                    'key' => $this->field->getColumn(),
                ],
            ]);
    }
}
