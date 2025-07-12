<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Directives\Filters\EqFilterDirective;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Directives\Filters\LikeFilterDirective;
use GraphQL\Type\Definition\Type;

/**
 * Class StringField.
 */
abstract class StringField extends Field implements FilterableField
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    protected function type(): Type
    {
        return Type::string();
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
            new LikeFilterDirective($this, $this->type()),
        ];
    }
}
