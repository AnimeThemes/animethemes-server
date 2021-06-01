<?php declare(strict_types=1);

namespace App\Concerns\JsonApi;

use App\JsonApi\QueryParser;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait PerformsResourceQuery
 * @package App\Concerns\JsonApi
 */
trait PerformsResourceQuery
{
    use PerformsConstrainedEagerLoading;

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
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
