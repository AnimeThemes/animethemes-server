<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\Concerns\GraphQL\ResolvesDirectives;
use App\GraphQL\Definition\Fields\Field;
use GraphQL\Type\Definition\Type;
use Stringable;

abstract class FilterDirective implements Stringable
{
    use ResolvesDirectives;

    public function __construct(
        protected Field $field,
        protected Type $type,
    ) {}
}
