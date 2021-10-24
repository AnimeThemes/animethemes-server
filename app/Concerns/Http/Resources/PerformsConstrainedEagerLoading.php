<?php

declare(strict_types=1);

namespace App\Concerns\Http\Resources;

use App\Http\Api\Query;
use App\Http\Api\Scope\ScopeParser;
use App\Services\Http\Resources\DiscoverRelationCollection;
use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

/**
 * Trait PerformsConstrainedEagerLoading.
 */
trait PerformsConstrainedEagerLoading
{
    /**
     * Constrain eager loads by binding callbacks that filter on the relations.
     *
     * @param  Query  $query
     * @return array<string, Closure>
     */
    public static function performConstrainedEagerLoads(Query $query): array
    {
        $constrainedEagerLoads = [];

        $includeCriteria = $query->getIncludeCriteria(Str::singular(static::$wrap));

        $allowedIncludePaths = collect($includeCriteria?->getPaths());

        foreach ($allowedIncludePaths as $allowedIncludePath) {
            $scope = ScopeParser::parse($allowedIncludePath);
            $constrainedEagerLoads[$allowedIncludePath] = function (Relation $relation) use ($query, $scope) {
                $collectionInstance = DiscoverRelationCollection::byModel($relation->getQuery()->getModel());
                if ($collectionInstance !== null) {
                    foreach ($collectionInstance::schema()->filters() as $filter) {
                        $filter->applyFilter($query->getFilterCriteria(), $relation->getQuery(), $scope);
                    }
                }
            };
        }

        return $constrainedEagerLoads;
    }
}
