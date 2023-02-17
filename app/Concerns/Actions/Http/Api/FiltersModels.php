<?php

declare(strict_types=1);

namespace App\Concerns\Actions\Http\Api;

use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class FiltersModels.
 */
trait FiltersModels
{
    /**
     * Apply filters to the query builder.
     *
     * @param  Builder  $builder
     * @param  Query  $query
     * @param  Schema  $schema
     * @param  Scope  $scope
     * @return Builder
     */
    public function filter(Builder $builder, Query $query, Schema $schema, Scope $scope): Builder
    {
        foreach ($query->getFilterCriteria() as $criteria) {
            foreach ($schema->filters() as $filter) {
                if ($criteria->shouldFilter($filter, $scope)) {
                    $criteria->filter($builder, $filter, $query, $schema);
                }
            }
        }

        return $builder;
    }
}
