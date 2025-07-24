<?php

declare(strict_types=1);

namespace App\Concerns\Actions\Http\Api;

use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\Scope;
use Illuminate\Database\Eloquent\Builder;

trait SortsModels
{
    /**
     * Apply sorts to the query builder.
     */
    public function sort(Builder $builder, Query $query, Schema $schema, Scope $scope = new GlobalScope()): Builder
    {
        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach ($schema->sorts() as $sort) {
                if ($sortCriterion->shouldSort($sort, $scope)) {
                    $sortCriterion->sort($builder, $sort);
                }
            }
        }

        return $builder;
    }
}
