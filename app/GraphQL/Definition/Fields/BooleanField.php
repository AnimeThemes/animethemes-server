<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Definition\Directives\Filters\EqFilterDirective;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use GraphQL\Type\Definition\Type;

/**
 * Class BooleanField.
 */
abstract class BooleanField extends Field implements DisplayableField, FilterableField
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        return Type::boolean();
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @return bool
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }

    /**
     * The directives available for this filter.
     *
     * @return FilterDirective[]
     */
    public function filterDirectives(): array
    {
        return [
            new EqFilterDirective($this, $this->type()),
        ];
    }
}
