<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    public function __construct(
        protected Field $field,
        protected mixed $defaultValue = null,
    ) {}

    public static function getValueWithResolvers(BaseType $baseType)
    {
        return collect($baseType->fieldClasses())
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

    abstract public function argument(): Argument;

    abstract public function filter(Builder $builder, mixed $value): Builder;
}
