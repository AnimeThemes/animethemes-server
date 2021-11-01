<?php

declare(strict_types=1);

namespace App\Concerns\Http\Resources;

use App\Http\Api\Criteria\Include\Criteria as IncludeCriteria;
use App\Http\Api\Scope\ScopeParser;
use App\Services\Http\Resources\DiscoverRelationCollection;
use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/**
 * Trait PerformsConstrainedEagerLoading.
 */
trait PerformsConstrainedEagerLoading
{
    /**
     * Constrain eager loads by binding callbacks that filter on the relations.
     *
     * @param  IncludeCriteria|null  $includeCriteria
     * @param  Collection  $filterCriteria
     * @return array<string, Closure>
     */
    public static function performConstrainedEagerLoads(
        ?IncludeCriteria $includeCriteria,
        Collection $filterCriteria
    ): array {
        $constrainedEagerLoads = [];

        $allowedIncludePaths = collect($includeCriteria?->getPaths());

        foreach ($allowedIncludePaths as $allowedIncludePath) {
            $scope = ScopeParser::parse($allowedIncludePath);
            $constrainedEagerLoads[$allowedIncludePath] = function (Relation $relation) use ($filterCriteria, $scope) {
                $collectionInstance = DiscoverRelationCollection::byModel($relation->getQuery()->getModel());
                if ($collectionInstance !== null) {
                    foreach ($collectionInstance::schema()->filters() as $filter) {
                        $filter->applyFilter($filterCriteria, $relation->getQuery(), $scope);
                    }
                }
            };
        }

        return $constrainedEagerLoads;
    }
}
