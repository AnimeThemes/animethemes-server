<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Filters;

use App\GraphQL\Definition\Filters\Directives\FilterDirective;
use App\GraphQL\Definition\Filters\Directives\GreaterFilterDirective;
use App\GraphQL\Definition\Filters\Directives\InFilterDirective;
use App\GraphQL\Definition\Filters\Directives\LesserFilterDirective;
use App\GraphQL\Definition\Filters\Directives\NotInFilterDirective;
use GraphQL\Type\Definition\Type;

/**
 * Class IntFilter.
 */
class IntFilter extends Filter
{
    /**
     * The directives available for this filter.
     *
     * @return array<int, FilterDirective>
     */
    protected function directives(): array
    {
        return [
            new InFilterDirective($this->field, Type::int()),
            new NotInFilterDirective($this->field, Type::int()),
            new LesserFilterDirective($this->field, Type::int()),
            new GreaterFilterDirective($this->field, Type::int()),
        ];
    }
}
