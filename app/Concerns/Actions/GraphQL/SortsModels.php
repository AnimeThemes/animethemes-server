<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\Contracts\GraphQL\EnumSort;
use App\GraphQL\Argument\SortArgument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait SortsModels
{
    public function sort(Builder $builder, array $args): Builder
    {
        /** @var EnumSort[] $sorts */
        $sorts = Arr::get($args, SortArgument::ARGUMENT, []);

        foreach ($sorts as $sort) {
            $sort->getSortCriteria()->sort($builder);
        }

        return $builder;
    }
}
