<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Unions\BaseUnion;
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
