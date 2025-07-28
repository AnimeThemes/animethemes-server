<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Enums\GraphQL\SortType;
use App\GraphQL\Support\Directives\Filters\EqFilterDirective;
use App\GraphQL\Support\Directives\Filters\FilterDirective;
use App\GraphQL\Support\Directives\Filters\GreaterFilterDirective;
use App\GraphQL\Support\Directives\Filters\InFilterDirective;
use App\GraphQL\Support\Directives\Filters\LesserFilterDirective;
use App\GraphQL\Support\Directives\Filters\NotInFilterDirective;
use GraphQL\Type\Definition\Type;

abstract class IntField extends Field implements DisplayableField, FilterableField, SortableField
{
    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::int();
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
            new InFilterDirective($this, $this->type()),
            new NotInFilterDirective($this, $this->type()),
            new LesserFilterDirective($this, $this->type()),
            new GreaterFilterDirective($this, $this->type()),
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
