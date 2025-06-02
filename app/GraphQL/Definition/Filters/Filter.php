<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Filters;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Filters\Directives\FilterDirective;

/**
 * Class Filter.
 */
abstract class Filter
{
    /**
     * @param  Field  $field
     */
    public function __construct(
        protected Field $field,
    ) {
    }

    /**
     * Get the arguments available for a field.
     *
     * @return array
     */
    public function toArray(): array
    {
        return collect($this->directives())
            ->map(fn (FilterDirective $directive) => $directive->toString())
            ->toArray();
    }

    /**
     * The directives available for this filter.
     *
     * @return array<int, FilterDirective>
     */
    abstract protected function directives(): array;
}
