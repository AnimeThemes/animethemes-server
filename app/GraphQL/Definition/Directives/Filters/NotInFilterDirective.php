<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\GraphQL\Definition\Argument\Argument;
use GraphQL\Type\Definition\Type;

class NotInFilterDirective extends FilterDirective
{
    /**
     * The argument for the filter directive.
     */
    public function argument(): Argument
    {
        return new Argument(
            $this->field->getName().'_not_in',
            Type::listOf($this->type),
            [
                'notIn' => [
                    'key' => $this->field->getColumn(),
                ],
            ],
        );
    }
}
