<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Directives\Filters\GreaterFilterDirective;
use App\GraphQL\Definition\Directives\Filters\LesserFilterDirective;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;

/**
 * Class DateTimeTzField.
 */
abstract class DateTimeTzField extends Field implements DisplayableField, FilterableField
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        return app(TypeRegistry::class)->get('DateTimeTz');
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
            new LesserFilterDirective($this, $this->type()),
            new GreaterFilterDirective($this, $this->type()),
        ];
    }
}
