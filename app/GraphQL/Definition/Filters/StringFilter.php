<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Filters;

use App\GraphQL\Definition\Filters\Directives\FilterDirective;
use App\GraphQL\Definition\Filters\Directives\EqFilterDirective;
use App\GraphQL\Definition\Filters\Directives\LikeFilterDirective;
use GraphQL\Type\Definition\Type;

/**
 * Class StringFilter.
 */
class StringFilter extends Filter
{
    /**
     * The directives available for this filter.
     *
     * @return array<int, FilterDirective>
     */
    protected function directives(): array
    {
        return [
            new EqFilterDirective($this->field, Type::string()),
            new LikeFilterDirective($this->field, Type::string()),
        ];
    }
}
