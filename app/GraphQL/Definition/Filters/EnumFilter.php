<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Filters;

use App\GraphQL\Definition\Filters\Directives\FilterDirective;
use App\GraphQL\Definition\Filters\Directives\InFilterDirective;
use App\GraphQL\Definition\Filters\Directives\NotInFilterDirective;

/**
 * Class EnumFilter.
 */
class EnumFilter extends Filter
{
    /**
     * The directives available for this filter.
     *
     * @return array<int, FilterDirective>
     */
    protected function directives(): array
    {
        return [
            new InFilterDirective($this->field, $this->field->getType()),
            new NotInFilterDirective($this->field, $this->field->getType()),
        ];
    }
}
