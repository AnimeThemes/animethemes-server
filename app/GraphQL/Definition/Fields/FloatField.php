<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Directives\Filters\GreaterFilterDirective;
use App\GraphQL\Definition\Directives\Filters\InFilterDirective;
use App\GraphQL\Definition\Directives\Filters\LesserFilterDirective;
use App\GraphQL\Definition\Directives\Filters\NotInFilterDirective;
use GraphQL\Type\Definition\Type;

/**
 * Class FloatField.
 */
abstract class FloatField extends Field implements FilterableField
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    protected function type(): Type
    {
        return Type::float();
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
