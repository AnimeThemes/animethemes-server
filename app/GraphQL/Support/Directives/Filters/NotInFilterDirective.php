<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Directives\Filters;

use App\GraphQL\Support\Argument;
use GraphQL\Type\Definition\Type;

final readonly class NotInFilterDirective extends FilterDirective
{
    /**
     * The argument for the filter directive.
     */
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_not_in', Type::listOf($this->type))
            ->directives([
                'notIn' => [
                    'key' => $this->field->getColumn(),
                ],
            ]);
    }
}
