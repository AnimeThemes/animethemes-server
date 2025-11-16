<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Filter;

use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Support\Filter\Filter;
use App\GraphQL\Support\Filter\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class FilterCriteria
{
    /**
     * @return Collection<int, Filter>
     */
    public static function getFilters(BaseType $baseType): Collection
    {
        $fields = $baseType->fieldClasses();

        return collect($fields)
            ->filter(fn (Field $field): bool => $field instanceof FilterableField)
            ->map(fn (Field&FilterableField $field): array => $field->getFilters())
            ->flatten()
            ->when(
                in_array(new DeletedAtField(), $fields),
                fn (Collection $collection) => $collection->push(new TrashedFilter())
            );
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

        $criteria = [];
        foreach ($args as $arg => $value) {
            $filter = $filters->first(fn (Filter $filter): bool => $filter->argument()->getName() === $arg);

            if ($filter instanceof Filter) {
                $criteria[] = $filter->criteria($value);
            }
        }

        return $criteria;
    }

    /**
     * Apply the filtering to the current Eloquent builder.
     */
    abstract public function filter(Builder $builder): Builder;
}
