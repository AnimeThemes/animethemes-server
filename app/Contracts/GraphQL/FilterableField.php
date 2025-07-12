<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

use App\GraphQL\Definition\Directives\Filters\FilterDirective;

/**
 * Interface FilterableField.
 */
interface FilterableField
{
    /**
     * The directives available for this field.
     *
     * @return FilterDirective[]
     */
    public function filterDirectives(): array;
}
