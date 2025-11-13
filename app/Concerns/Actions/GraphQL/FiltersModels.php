<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\GraphQL\Criteria\Filter\FilterCriteria;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Unions\BaseUnion;
use Illuminate\Database\Eloquent\Builder;

trait FiltersModels
{
    public function filter(Builder $builder, array $args, BaseType|BaseUnion $type): Builder
    {
        // union not supported yet
        if ($type instanceof BaseUnion) {
            return $builder;
        }

        $criterias = FilterCriteria::parse($type, $args);

        foreach ($criterias as $criteria) {
            $criteria->filter($builder);
        }

        return $builder;
    }
}
