<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Filters\Filter;
use App\GraphQL\Definition\Filters\IntFilter;
use GraphQL\Type\Definition\Type;

/**
 * Class IntField.
 */
abstract class IntField extends Field implements FilterableField
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    protected function type(): Type
    {
        return Type::int();
    }

    /**
     * Get the filter for this field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new IntFilter($this);
    }
}
