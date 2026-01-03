<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Filter;

use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\FilterArgument;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\TrashedFilter;
use App\GraphQL\Filter\WhereConditionsFilter;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\EloquentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class FilterCriteria
{
    public function __construct(
        protected Filter $filter,
        protected $value,
    ) {}

    /**
     * @return Collection<int, Filter>
     */
    public static function getFilters(BaseType $baseType): Collection
    {
        $fields = $baseType->fieldClasses();

        return collect($fields)
            ->filter(fn (Field $field): bool => $field instanceof FilterableField)
            ->map(fn (Field&FilterableField $field): Filter => $field->getFilter())
            ->flatten()
            ->when(
                $baseType instanceof EloquentType,
                /** @phpstan-ignore-next-line */
                fn (Collection $collection) => $collection->push(new WhereConditionsFilter($baseType))
            )
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
            $match = $filters
                ->map(function (Filter $filter) use ($arg): ?array {
                    $argument = collect($filter->getArguments())
                        ->first(fn (Argument $argument): bool => $argument->getName() === $arg);

                    return $argument ? [$filter, $argument] : null;
                })
                ->filter()
                ->first();

            [$filter, $argument] = $match;

            if ($filter instanceof Filter && Str::endsWith($arg, '_in')) {
                $criteria[] = new WhereInFilterCriteria($filter, $value, Str::endsWith($arg, '_not_in'));
                continue;
            }

            if ($filter instanceof TrashedFilter) {
                $criteria[] = new TrashedFilterCriteria($filter, $value);
                continue;
            }

            if ($filter instanceof WhereConditionsFilter && $type instanceof EloquentType) {
                $criteria[] = new WhereConditionsFilterCriteria($filter, $value, $type);
                continue;
            }

            if ($filter instanceof Filter && $argument instanceof FilterArgument) {
                $criteria[] = new WhereFilterCriteria($filter, $value, $argument);
                continue;
            }
        }

        return $criteria;
    }

    /**
     * Get the filter values.
     */
    public function getFilterValues(): array
    {
        $value = $this->value;

        if ($value instanceof Collection) {
            return $value->all();
        }

        return Arr::wrap($value);
    }

    /**
     * Apply the filtering to the current Eloquent builder.
     */
    abstract public function filter(Builder $builder): Builder;
}
