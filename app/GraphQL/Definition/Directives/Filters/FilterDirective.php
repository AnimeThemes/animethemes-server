<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\Concerns\GraphQL\ResolvesDirectives;
use App\GraphQL\Definition\Fields\Field;
use GraphQL\Type\Definition\Type;
use Stringable;

/**
 * Class FilterDirective.
 */
abstract class FilterDirective implements Stringable
{
    use ResolvesDirectives;

    /**
     * @param  Field  $field
     * @param  Type  $type
     */
    public function __construct(
        protected Field $field,
        protected Type $type,
    ) {}
}
