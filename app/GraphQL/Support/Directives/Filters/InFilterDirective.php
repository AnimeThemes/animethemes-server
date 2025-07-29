<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Directives\Filters;

use App\GraphQL\Support\Argument;
use GraphQL\Type\Definition\Type;

final readonly class InFilterDirective extends FilterDirective
{
    /**
     * The argument for the filter directive.
     */
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_in', Type::listOf(Type::nonNull($this->type)))
            ->directives([
                'in' => [
                    'key' => $this->field->getColumn(),
                ],
            ]);
    }
}
