<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\Concerns\GraphQL\ResolvesDirectives;
use App\GraphQL\Definition\Argument\Argument;
use App\GraphQL\Definition\Fields\Field;
use GraphQL\Type\Definition\Type;

abstract class FilterDirective
{
    use ResolvesDirectives;

    public function __construct(
        protected Field $field,
        protected Type $type,
    ) {}

    /**
     * The argument for the filter directive.
     */
    abstract public function argument(): Argument;
}
