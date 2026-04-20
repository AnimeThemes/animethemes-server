<?php

declare(strict_types=1);

namespace App\GraphQL\Builders;

use App\Contracts\GraphQL\EnumSort;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SortBuilder
{
    /**
     * @param  array<int, UnitEnum&EnumSort>  $sorts
     */
    public function __invoke(Builder $builder, array $sorts): Builder
    {
        foreach ($sorts as $sort) {
            $sort->getSortCriteria()->sort($builder);
        }

        return $builder;
    }
}
