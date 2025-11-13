<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\GraphQL\Criteria\Sort\SortCriteria;
use App\GraphQL\Schema\Enums\SortableColumns;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Unions\BaseUnion;
use App\GraphQL\Support\Argument\SortArgument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait SortsModels
{
    public function sort(Builder $builder, array $args, BaseType|BaseUnion $type): Builder
    {
        $sorts = Arr::get($args, SortArgument::ARGUMENT);

        if (blank($sorts)) {
            return $builder;
        }

        $criterias = Arr::get(new SortableColumns($type)->getAttributes(), 'criterias');

        foreach ($sorts as $sort) {
            /** @var SortCriteria $criteria */
            $criteria = Arr::get($criterias, $sort);

            $criteria->sort($builder);
        }

        return $builder;
    }
}
