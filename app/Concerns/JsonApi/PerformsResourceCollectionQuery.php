<?php

namespace App\Concerns\JsonApi;

use App\JsonApi\QueryParser;
use Illuminate\Support\Str;

trait PerformsResourceCollectionQuery
{
    use PerformsConstrainedEagerLoading;

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
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static function allowedSortFields()
    {
        return [];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @var array
     */
    public static function filters()
    {
        return [];
    }

    /**
     * Resolve the model query builder from collection class name.
     * We are assuming a convention of "{Model}Collection".
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function queryBuilder()
    {
        $model = Str::replaceLast('Collection', '', class_basename(static::class));

        $modelClass = "\\App\\Models\\{$model}";

        return $modelClass::query();
    }

    /**
     * Perform query to prepare models for resource collection.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return static
     */
    public static function performQuery(QueryParser $parser)
    {
        // initialize builder
        $builder = static::queryBuilder();

        // eager load relations with constraints
        $builder = $builder->with(static::performConstrainedEagerLoads($parser));

        // apply filters
        foreach (static::filters() as $filterClass) {
            $filter = new $filterClass($parser);
            $builder = $filter->applyFilter($builder);
        }

        // apply sorts
        foreach ($parser->getSorts() as $field => $isAsc) {
            if (in_array(Str::lower($field), static::allowedSortFields())) {
                $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
            }
        }

        // paginate
        $collection = $builder->jsonPaginate();

        // return paginated resource collection
        return static::make($collection, $parser);
    }
}
