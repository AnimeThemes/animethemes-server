<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\Http\Resources\PerformsConstrainedEagerLoading;
use App\Http\Api\QueryParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class BaseCollection.
 */
abstract class BaseCollection extends ResourceCollection
{
    use PerformsConstrainedEagerLoading;

    /**
     * Sparse field set specified by the client.
     *
     * @var QueryParser
     */
    protected QueryParser $parser;

    /**
     * Indicates if all existing request query parameters should be added to pagination links.
     *
     * @var bool
     */
    protected $preserveAllQueryParameters = true;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @param mixed $parser
     * @return void
     */
    public function __construct(mixed $resource, mixed $parser)
    {
        parent::__construct($resource);

        $this->parser = $parser;
    }

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
     * The sort field names a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedSortFields(): array
    {
        return [];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return string[]
     */
    public static function filters(): array
    {
        return [];
    }

    /**
     * Get the model query builder.
     *
     * @return Builder|null
     */
    protected static function queryBuilder(): ?Builder
    {
        $collection = static::make(new MissingValue(), QueryParser::make());
        $collectsClass = $collection->collects;

        if (! empty($collectsClass)) {
            return $collectsClass::query();
        }

        return null;
    }

    /**
     * Perform query to prepare models for resource collection.
     *
     * @param QueryParser $parser
     * @return static
     */
    public static function performQuery(QueryParser $parser): static
    {
        // initialize builder, returning early if not resolved
        $builder = static::queryBuilder();
        if ($builder === null) {
            return static::make(Collection::make(), $parser);
        }

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
