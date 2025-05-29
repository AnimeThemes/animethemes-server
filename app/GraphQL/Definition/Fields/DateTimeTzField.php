<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Filters\DateTimeTzFilter;
use App\GraphQL\Definition\Filters\Filter;
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
    protected function type(): Type
    {
        return app(TypeRegistry::class)->get('DateTimeTz');
    }

    /**
     * Get the filter for this field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new DateTimeTzFilter($this);
    }
}
