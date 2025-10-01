<?php

declare(strict_types=1);

namespace App\Concerns\Actions\Http\Api;

use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\ScopeParser;
use Illuminate\Database\Eloquent\Relations\Relation;
use RuntimeException;

trait ConstrainsEagerLoads
{
    use AggregatesFields;
    use FiltersModels;
    use SelectsFields;
    use SortsModels;

    /**
     * Constrain eager loads by binding callbacks that filter on the relations.
     */
    protected function constrainEagerLoads(Query $query, Schema $schema): array
    {
        $constrainedEagerLoads = [];

        $includeCriteria = $query->getIncludeCriteria($schema->type());
        if (! $includeCriteria instanceof Criteria) {
            return $constrainedEagerLoads;
        }

        foreach ($includeCriteria->getPaths() as $allowedIncludePath) {
            $relationSchema = $schema->relation($allowedIncludePath);
            throw_if(! $relationSchema instanceof Schema, new RuntimeException("Unknown relation '$allowedIncludePath' for type '{$schema->type()}'."));

            $scope = ScopeParser::parse($allowedIncludePath);
            $constrainedEagerLoads[$allowedIncludePath] = function (Relation $relation) use ($query, $scope, $relationSchema): void {
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
