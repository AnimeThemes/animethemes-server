<?php

namespace App\Concerns\JsonApi;

use App\JsonApi\QueryParser;
use Illuminate\Database\Eloquent\Model;

trait PerformsResourceQuery
{
    use PerformsConstrainedEagerLoading;

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
     */
    public static function allowedIncludePaths()
    {
        return [];
    }

    /**
     * Perform query to prepare model for resource.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \App\JsonApi\QueryParser $parser
     * @return static
     */
    public static function performQuery(Model $model, QueryParser $parser)
    {
        return static::make($model->load(static::performConstrainedEagerLoads($parser)), $parser);
    }
}
