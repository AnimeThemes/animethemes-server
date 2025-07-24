<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Directives\Filters\GreaterFilterDirective;
use App\GraphQL\Definition\Directives\Filters\InFilterDirective;
use App\GraphQL\Definition\Directives\Filters\LesserFilterDirective;
use App\GraphQL\Definition\Directives\Filters\NotInFilterDirective;
use GraphQL\Type\Definition\Type;

abstract class FloatField extends Field implements DisplayableField, FilterableField
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        return Type::float();
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
            new InFilterDirective($this, $this->type()),
            new NotInFilterDirective($this, $this->type()),
            new LesserFilterDirective($this, $this->type()),
            new GreaterFilterDirective($this, $this->type()),
        ];
    }
}
