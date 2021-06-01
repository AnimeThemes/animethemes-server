<?php

declare(strict_types=1);

namespace App\Concerns\JsonApi;

use App\JsonApi\QueryParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Trait PerformsResourceCollectionQuery.
 */
trait PerformsResourceCollectionQuery
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
     * The sort field names a client is allowed to request.
     *
     * @return array
     */
    public static function allowedSortFields(): array
    {
        return [];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return array
     */
    public static function filters(): array
    {
        return [];
    }

    /**
     * Resolve the model query builder from collection class name.
     * We are assuming a convention of "{Model}Collection".
     *
     * @return Builder
     */
    protected static function queryBuilder(): Builder
    {
        $model = Str::replaceLast('Collection', '', class_basename(static::class));

        $modelClass = "\\App\\Models\\{$model}";

        return $modelClass::query();
    }

    /**
     * Perform query to prepare models for resource collection.
     *
     * @param QueryParser $parser
     * @return static
     */
    public static function performQuery(QueryParser $parser): static
    {
        // initialize builder
        $builder = static::queryBuilder();

        // eager load relations with constraints
        $builder = $builder->with(static::performConstrainedEagerLoads($parser));

        // apply filters
        foreach (static::filters() as $filterClass) {
            $filter = new $filterClass($parser);
            $builder = $filter->scope(Str::singular(static::$wrap))->applyFilter($builder);
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
