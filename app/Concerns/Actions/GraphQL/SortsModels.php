<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\GraphQL\Criteria\Sort\SortCriteria;
use App\GraphQL\Schema\Enums\SortableColumns;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Unions\BaseUnion;
use App\GraphQL\Support\Argument\SortArgument;
use App\GraphQL\Support\Relations\Relation as GraphQLRelation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

trait SortsModels
{
    public function sort(Builder $builder, array $args, BaseType|BaseUnion $type, ?Relation $relation = null, ?GraphQLRelation $graphqlRelation = null): Builder
    {
        $sorts = Arr::get($args, SortArgument::ARGUMENT);

        if (blank($sorts)) {
            return $builder;
        }

        $relation = $relation instanceof BelongsToMany
            ? $relation
            : null;

        $criterias = Arr::get(new SortableColumns($type, $graphqlRelation?->getPivotType(), $relation)->getAttributes(), 'criterias');

        foreach ($sorts as $sort) {
            /** @var SortCriteria $criteria */
            $criteria = Arr::get($criterias, $sort);

            $criteria->sort($builder);
        }

        return $builder;
    }
}
