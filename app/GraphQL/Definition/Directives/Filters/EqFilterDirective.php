<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\GraphQL\Definition\Argument\Argument;

class EqFilterDirective extends FilterDirective
{
    /**
     * The argument for the filter directive.
     */
    public function argument(): Argument
    {
        return new Argument(
            $this->field->getName(),
            $this->type,
            false,
            [
                'eq' => [
                    'key' => $this->field->getColumn(),
                ],
            ],
        );
    }
}
