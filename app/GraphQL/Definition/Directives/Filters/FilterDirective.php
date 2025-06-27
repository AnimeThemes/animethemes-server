<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Directives\Filters;

use App\GraphQL\Definition\Fields\Field;
use GraphQL\Type\Definition\Type;

/**
 * Class FilterDirective.
 */
abstract class FilterDirective
{
    /**
     * @param  Field  $field
     * @param  Type  $type
     */
    public function __construct(
        protected Field $field,
        protected Type $type,
    ) {}

    /**
     * Create the argument for the directive.
     *
     * @return string
     */
    abstract public function toString(): string;
}
