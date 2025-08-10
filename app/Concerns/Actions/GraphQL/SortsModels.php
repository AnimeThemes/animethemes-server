<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\Enums\GraphQL\SortType;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\SortArgument;
use App\GraphQL\Support\Sort\RandomSort;
use App\GraphQL\Support\Sort\Sort;
use App\GraphQL\Support\SortableColumns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

trait SortsModels
{
    /**
     * Apply sorts to the query builder.
     */
    public function sort(Builder $builder, array $args, BaseType $type): Builder
    {
        $sorts = Arr::get($args, SortArgument::ARGUMENT);

        if (blank($sorts)) {
            return $builder;
        }

        $resolvers = new SortableColumns($type)->getValuesWithResolver();

        $this->applySort($builder, $sorts, $resolvers);

        return $builder;
    }

    /**
     * @param  array<string, string>  $args
     * @param  array<string, array<string, mixed>>  $resolvers
     *
     * @throws InvalidArgumentException
     */
    protected function applySort(Builder $builder, array $sorts, array $resolvers): Builder
    {
        foreach ($sorts as $sort) {
            if ($sort === RandomSort::CASE) {
                $builder->inRandomOrder();
                continue;
            }

            $resolver = Arr::get($resolvers, Str::remove('_DESC', $sort));

            $direction = Sort::resolveFromEnumCase($sort);

            $column = Arr::get($resolver, SortableColumns::RESOLVER_COLUMN);
            $sortType = Arr::get($resolver, SortableColumns::RESOLVER_SORT_TYPE);

            if ($sortType === SortType::ROOT) {
                $builder->orderBy($column, $direction);
            }

            if ($sortType === SortType::AGGREGATE) {
                $relation = Arr::get($resolver, SortableColumns::RESOLVER_RELATION);
                if ($relation === null) {
                    throw new InvalidArgumentException("The 'relation' argument is required for the @{$this->name()} directive with aggregate sort type.");
                }

                $builder->withAggregate([
                    "$relation as {$relation}_value" => function ($query) use ($direction) {
                        $query->orderBy('value', $direction);
                    },
                ], 'value');

                $builder->orderBy("{$relation}_value", $direction);
            }
        }

        return $builder;
    }
}
