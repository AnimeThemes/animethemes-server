<?php

declare(strict_types=1);

namespace App\Concerns\Http\Api;

use App\Http\Api\QueryParser;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait PerformsResourceQuery.
 */
trait PerformsResourceQuery
{
    use PerformsConstrainedEagerLoading;

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [];
    }

    /**
     * Perform query to prepare model for resource.
     *
     * @param Model $model
     * @param QueryParser $parser
     * @return static
     */
    public static function performQuery(Model $model, QueryParser $parser): static
    {
        return static::make($model->load(static::performConstrainedEagerLoads($parser)), $parser);
    }
}
