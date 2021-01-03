<?php

namespace App\JsonApi\Traits;

use App\JsonApi\QueryParser;
use Illuminate\Support\Str;

trait PerformsConstrainedEagerLoading
{
    /**
     * Constrain eager loads by binding callbacks that filter on the relations.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return array
     */
    protected static function performConstrainedEagerLoads(QueryParser $parser)
    {
        $constrainedEagerLoads = [];

        $allowedIncludePaths = $parser->getIncludePaths(static::allowedIncludePaths());

        foreach ($allowedIncludePaths as $allowedIncludePath) {
            $relation = static::relation($allowedIncludePath);

            $constrainedEagerLoads[$allowedIncludePath] = function ($query) use ($parser, $relation) {
                foreach ($relation::filters() as $filterClass) {
                    $filter = new $filterClass($parser);
                    $filter->applyRelationFilter($query);
                }
            };
        }

        return $constrainedEagerLoads;
    }

    /**
     * Resolve the related collection resource from the relation name.
     * We are assuming a convention of "{Relation}Collection".
     *
     * @param string $allowedIncludePath
     * @return string
     */
    protected static function relation($allowedIncludePath)
    {
        $relatedModel = Str::singular(Str::of($allowedIncludePath)->explode('.')->last());

        return "\\App\\Http\\Resources\\{$relatedModel}Collection";
    }
}
