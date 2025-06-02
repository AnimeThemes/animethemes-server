<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Filters\Filter;
use App\GraphQL\Definition\Filters\StringFilter;
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
     * Get the filter for this field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new StringFilter($this);
    }
}
