<?php

declare(strict_types=1);

namespace App\Concerns\Http\Resources;

use App\Http\Api\QueryParser;
use App\Services\Http\Resources\DiscoverRelationCollection;
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
     * @param QueryParser $parser
     * @return array
     */
    protected static function performConstrainedEagerLoads(QueryParser $parser): array
    {
        $constrainedEagerLoads = [];

        $allowedIncludePaths = $parser->getIncludePaths(static::allowedIncludePaths(), Str::singular(static::$wrap));

        foreach ($allowedIncludePaths as $allowedIncludePath) {
            $constrainedEagerLoads[$allowedIncludePath] = function (Relation $query) use ($parser) {
                $collectionClass = DiscoverRelationCollection::byModel($query->getQuery()->getModel());
                if ($collectionClass !== null) {
                    foreach ($collectionClass::filters() as $filterClass) {
                        $filter = new $filterClass($parser);
                        $filter->scope(Str::singular($collectionClass::$wrap))->applyFilter($query->getQuery());
                    }
                }
            };
        }

        return $constrainedEagerLoads;
    }
}
