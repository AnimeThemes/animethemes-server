<?php

namespace App\Http\Resources;

use App\JsonApi\QueryParser;
use Illuminate\Database\Eloquent\Model;

trait PerformsResourceQuery
{
    /**
     * The include paths a client is allowed to request.
     *
     * @var array
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
        // TODO: constrain eager loads for deep filtering
        return static::make($model->load($parser->getIncludePaths(static::allowedIncludePaths())), $parser);
    }
}
