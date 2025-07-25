<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Definition\Directives\Filters\EqFilterDirective;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use GraphQL\Type\Definition\Type;

abstract class BooleanField extends Field implements DisplayableField, FilterableField, SortableField
{
    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::boolean();
    }

    /**
     * Determine if the field should be displayed to the user.
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

    /**
     * The sort type of the field.
     */
    public function sortType(): SortType
    {
        return SortType::ROOT;
    }
}
