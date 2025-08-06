<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Directives\Filters;

use App\Concerns\GraphQL\ResolvesDirectives;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;

abstract readonly class FilterDirective
{
    use ResolvesDirectives;

    protected Type $type;

    public function __construct(
        protected Field $field,
        protected ?string $defaultValue = null,
    ) {
        $this->type = $field->type();
    }

    /**
     * The argument for the filter directive.
     */
    abstract public function argument(): Argument;
}
