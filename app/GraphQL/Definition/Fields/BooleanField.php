<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Directives\Filters\EqFilterDirective;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use GraphQL\Type\Definition\Type;

/**
 * Class BooleanField.
 */
abstract class BooleanField extends Field implements FilterableField
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    protected function type(): Type
    {
        return Type::boolean();
    }

    /**
     * The directives available for this filter.
     *
     * @return array<int, FilterDirective>
     */
    public function filterDirectives(): array
    {
        return [
            new EqFilterDirective($this, $this->type()),
        ];
    }
}
