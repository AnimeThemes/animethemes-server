<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

use App\GraphQL\Support\Directives\Filters\FilterDirective;

interface FilterableField
{
    /**
     * The directives available for this field.
     *
     * @return FilterDirective[]
     */
    public function filterDirectives(): array;
}
