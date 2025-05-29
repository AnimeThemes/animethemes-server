<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Filters;

use App\GraphQL\Definition\Filters\Directives\FilterDirective;
use App\GraphQL\Definition\Filters\Directives\GreaterFilterDirective;
use App\GraphQL\Definition\Filters\Directives\LesserFilterDirective;
use Nuwave\Lighthouse\Schema\TypeRegistry;

/**
 * Class DateTimeTzFilter.
 */
class DateTimeTzFilter extends Filter
{
    /**
     * The directives available for this filter.
     *
     * @return array<int, FilterDirective>
     */
    protected function directives(): array
    {
        $type = app(TypeRegistry::class)->get('DateTimeTz');

        return [
            new LesserFilterDirective($this->field, $type),
            new GreaterFilterDirective($this->field, $type),
        ];
    }
}
