<?php

declare(strict_types=1);

namespace App\Concerns\Actions\Http\Api;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\ScopeParser;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use RuntimeException;

/**
 * Trait ConstrainsEagerLoads.
 */
trait ConstrainsEagerLoads
{
    use AggregatesFields;
    use FiltersModels;
    use SelectsFields;
    use SortsModels;

    /**
     * Constrain eager loads by binding callbacks that filter on the relations.
     *
     * @param  Query  $query
     * @param  Schema  $schema
     * @return array
     */
    protected function constrainEagerLoads(Query $query, Schema $schema): array
    {
        $constrainedEagerLoads = [];

        $includeCriteria = $query->getIncludeCriteria($schema->type());
        if ($includeCriteria === null) {
            return $constrainedEagerLoads;
        }

        // The intermediate paths created in the criteria class should not be
        // included if the intermediate path is not an allowed include.
        $validPaths = Arr::where($includeCriteria->getPaths()->toArray(), fn (string $path) => in_array($path, $schema->allowedIncludes()));

        $paths = collect($validPaths);

        foreach ($paths as $allowedIncludePath) {
            $relationSchema = $schema->relation($allowedIncludePath);
            if ($relationSchema === null) {
                throw new RuntimeException("Unknown relation '$allowedIncludePath' for type '{$schema->type()}'.");
            }

            $scope = ScopeParser::parse($allowedIncludePath);
            $constrainedEagerLoads[$allowedIncludePath] = function (Relation $relation) use ($query, $scope, $relationSchema) {
                $relationBuilder = $relation->getQuery();

                $this->select($relationBuilder, $query, $relationSchema);

                $this->withAggregates($relationBuilder, $query, $relationSchema);

                $this->filter($relationBuilder, $query, $relationSchema, $scope);

                $this->sort($relationBuilder, $query, $relationSchema, $scope);
            };
        }

        return $constrainedEagerLoads;
    }
}
