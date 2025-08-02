<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Directives\Filters;

use App\GraphQL\Support\Argument\Argument;

final readonly class EqFilterDirective extends FilterDirective
{
    /**
     * The argument for the filter directive.
     */
    public function argument(): Argument
    {
        return new Argument($this->field->getName(), $this->type)
            ->directives([
                'eq' => [
                    'key' => $this->field->getColumn(),
                ],
            ]);
    }
}
