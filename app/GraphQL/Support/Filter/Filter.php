<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    public function __construct(
        protected Field $field,
        protected ?string $defaultValue = null,
    ) {}

    /**
     * Get the argument to apply the filter.
     */
    abstract public function argument(): Argument;

    public static function getValueWithResolvers(BaseType $rebingType)
    {
        return collect($rebingType->fieldClasses())
            ->filter(fn (Field $field) => $field instanceof FilterableField)
            ->flatMap(function (Field&FilterableField $field) {
                return collect($field->getFilters())
                    ->mapWithKeys(fn (Filter $filter) => [
                        $filter->argument()->name => [
                            'filter' => $filter,
                        ],
                    ]);
            });
    }

    /**
     * Apply the filter to the builder.
     */
    abstract public function apply(Builder $builder, mixed $value): Builder;
}
