<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Directives\Filters\GreaterFilterDirective;
use App\GraphQL\Definition\Directives\Filters\LesserFilterDirective;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;

/**
 * Class DateTimeTzField.
 */
abstract class DateTimeTzField extends Field implements FilterableField
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
