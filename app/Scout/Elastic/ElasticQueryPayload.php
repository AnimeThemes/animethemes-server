<?php

namespace App\Scout\Elastic;

use App\Enums\JsonApi\PaginationStrategy;
use App\JsonApi\QueryParser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

abstract class ElasticQueryPayload
{
    /**
     * Filter set specified by the client.
     *
     * @var \App\JsonApi\QueryParser
     */
    protected $parser;

    /**
     * Relations to eager load for the matches.
     *
     * @var array
     */
    protected $relations;

    /**
     * The strategy by which our matches are paginated.
     *
     * @var PaginationStrategy
     */
    protected $paginationStrategy;

    /**
     * Create a new query payload instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @param array $relations
     */
    final public function __construct(
        QueryParser $parser,
        array $relations,
        PaginationStrategy $paginationStrategy
    ) {
        $this->parser = $parser;
        $this->relations = $relations;
        $this->paginationStrategy = $paginationStrategy;
    }

    /**
     * Create a new query payload instance.
     *
     * @param  mixed  ...$parameters
     * @return static
     */
    public static function make(...$parameters)
    {
        return new static(...$parameters);
    }

    /**
     * Determine if this search should be performed.
     *
     * @return bool
     */
    protected function shouldPerformSearch()
    {
        return $this->parser->hasSearch();
    }

    /**
     * Perform search if condition is met, otherwise return empty collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function performSearch()
    {
        if ($this->shouldPerformSearch()) {
            return $this->doPerformSearch();
        }

        return Collection::make();
    }

    /**
     * Build and execute Elasticsearch query.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\ElasticScoutDriverPlus\Paginator
     */
    public function doPerformSearch()
    {
        // initialize builder with payload for matches
        $builder = $this->buildQuery();

        // eager load relations with constraints
        $builder = $builder->load($this->relations);

        // TODO: apply filters

        // limit page size
        if (PaginationStrategy::LIMIT()->is($this->paginationStrategy)) {
            $builder = $builder->size($this->parser->getLimit());
        }

        // paginate
        if (PaginationStrategy::OFFSET()->is($this->paginationStrategy)) {
            $maxResults = $maxResults ?? Config::get('json-api-paginate.max_results');
            $defaultSize = $defaultSize ?? Config::get('json-api-paginate.default_size');
            $numberParameter = Config::get('json-api-paginate.number_parameter');
            $sizeParameter = Config::get('json-api-paginate.size_parameter');
            $paginationParameter = Config::get('json-api-paginate.pagination_parameter');

            $size = (int) request()->input($paginationParameter.'.'.$sizeParameter, $defaultSize);

            $size = $size > $maxResults ? $maxResults : $size;

            $paginator = $builder->paginate($size, $paginationParameter.'.'.$numberParameter)
                ->setPageName($paginationParameter.'['.$numberParameter.']')
                ->appends(Arr::except(request()->input(), $paginationParameter.'.'.$numberParameter));

            $paginator->setCollection($paginator->models());

            return $paginator;
        }

        return $builder->execute()->models();
    }

    /**
     * Build Elasticsearch query.
     *
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    abstract protected function buildQuery();
}
