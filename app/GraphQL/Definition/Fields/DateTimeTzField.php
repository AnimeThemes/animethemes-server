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
use App\GraphQL\Support\Directives\Filters\LesserFilterDirective;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;

abstract class DateTimeTzField extends Field implements DisplayableField, FilterableField, SortableField
{
    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return app(TypeRegistry::class)->get('DateTimeTz');
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
            new EqFilterDirective($this),
            new LesserFilterDirective($this),
            new GreaterFilterDirective($this),
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
