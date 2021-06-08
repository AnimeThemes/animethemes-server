<?php

declare(strict_types=1);

namespace App\Concerns\Http\Api;

use App\Http\Api\QueryParser;
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
            $relation = static::relation($allowedIncludePath);

            $constrainedEagerLoads[$allowedIncludePath] = function (Relation $query) use ($parser, $relation) {
                foreach ($relation::filters() as $filterClass) {
                    $filter = new $filterClass($parser);
                    $filter->scope(Str::singular($relation::$wrap))->applyFilter($query->getQuery());
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
    protected static function relation(string $allowedIncludePath): string
    {
        $relatedModel = Str::ucfirst(Str::singular(Str::of($allowedIncludePath)->explode('.')->last()));

        return "\\App\\Http\\Resources\\{$relatedModel}Collection";
    }
}
