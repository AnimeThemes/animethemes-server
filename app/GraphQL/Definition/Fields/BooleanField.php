<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Filters\BooleanFilter;
use App\GraphQL\Definition\Filters\Filter;
use GraphQL\Type\Definition\Type;

/**
 * Class BooleanField.
 */
abstract class BooleanField extends Field implements FilterableField
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    protected function type(): Type
    {
        return Type::boolean();
    }

    /**
     * Get the filter for this field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new BooleanFilter($this);
    }
}
