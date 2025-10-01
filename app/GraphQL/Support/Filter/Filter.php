<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class Filter
{
    public function __construct(
        protected Field $field,
        protected mixed $defaultValue = null,
    ) {}

    public static function getValueWithResolvers(BaseType $baseType): Collection
    {
        return collect($baseType->fieldClasses())
            ->filter(fn (Field $field): bool => $field instanceof FilterableField)
            ->flatMap(fn (Field&FilterableField $field) => collect($field->getFilters())
                ->mapWithKeys(fn (Filter $filter): array => [
                    $filter->argument()->name => [
                        'filter' => $filter,
                    ],
                ]));
    }

    abstract public function argument(): Argument;

    abstract public function filter(Builder $builder, mixed $value): Builder;
}
