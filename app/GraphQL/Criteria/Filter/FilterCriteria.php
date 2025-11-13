<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Filter;

use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Support\Argument\TrashedArgument;
use App\GraphQL\Support\Filter\Filter;
use App\GraphQL\Support\Filter\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class FilterCriteria
{
    /**
     * @return Collection<string, Filter>
     */
    public static function getFilters(BaseType $baseType): Collection
    {
        return collect($baseType->fieldClasses())
            ->filter(fn (Field $field): bool => $field instanceof FilterableField)
            ->flatMap(fn (Field&FilterableField $field) => collect($field->getFilters())
                ->mapWithKeys(fn (Filter $filter): array => [$filter->argument()->name => $filter]))
            ->put(new TrashedArgument()->name, new TrashedFilter());
    }

    /**
     * Parse filter criteria from arguments.
     *
     * @param  array<string, mixed>  $args
     * @return FilterCriteria[]
     */
    public static function parse(BaseType $type, array $args): array
    {
        $filters = static::getFilters($type);

        $criterias = [];
        foreach ($args as $arg => $value) {
            $filter = Arr::get($filters, $arg);

            if ($filter instanceof Filter) {
                $criterias[] = $filter->criteria($value);
            }
        }

        return $criterias;
    }

    /**
     * Apply the filtering to the current Eloquent builder.
     */
    abstract public function filter(Builder $builder): Builder;
}
