<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\Enums\GraphQL\TrashedFilter;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Unions\BaseUnion;
use App\GraphQL\Support\Argument\TrashedArgument;
use App\GraphQL\Support\Filter\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait FiltersModels
{
    public function filter(Builder $builder, array $args, BaseType|BaseUnion $type): Builder
    {
        // union not supported yet
        if ($type instanceof BaseUnion) {
            return $builder;
        }

        $trashed = Arr::get($args, TrashedArgument::ARGUMENT);

        match ($trashed) {
            /** @phpstan-ignore-next-line */
            TrashedFilter::WITH => $builder->withTrashed(),
            /** @phpstan-ignore-next-line */
            TrashedFilter::WITHOUT => $builder->withoutTrashed(),
            /** @phpstan-ignore-next-line */
            TrashedFilter::ONLY => $builder->onlyTrashed(),
            default => null,
        };

        $resolvers = Filter::getValueWithResolvers($type);

        foreach ($args as $arg => $value) {
            $valueResolver = Arr::get($resolvers, $arg);

            if ($valueResolver === null) {
                continue;
            }

            /** @var Filter $filter */
            $filter = Arr::get($valueResolver, 'filter');

            $filter->apply($builder, $value);
        }

        return $builder;
    }
}
