<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Filters;

use App\GraphQL\Definition\Filters\Directives\EqFilterDirective;
use App\GraphQL\Definition\Filters\Directives\FilterDirective;
use GraphQL\Type\Definition\Type;

/**
 * Class BooleanFilter.
 */
class BooleanFilter extends Filter
{
    /**
     * The directives available for this filter.
     *
     * @return array<int, FilterDirective>
     */
    protected function directives(): array
    {
        return [
            new EqFilterDirective($this->field, Type::boolean()),
        ];
    }
}
