<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Paging;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Parser\PagingParser;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Paginator as ElasticsearchPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

/**
 * Class OffsetCriteria.
 */
class OffsetCriteria extends Criteria
{
    public const SIZE_PARAM = 'size';

    public const NUMBER_PARAM = 'number';

    /**
     * Get the intended pagination strategy.
     *
     * @return PaginationStrategy
     */
    public function getStrategy(): PaginationStrategy
    {
        return PaginationStrategy::OFFSET();
    }

    /**
     * Paginate the query.
     *
     * @param Builder $builder
     * @return Collection|Paginator
     */
    public function apply(Builder $builder): Collection | Paginator
    {
        $pageNameQuery = Str::of(PagingParser::$param)
            ->append('.')
            ->append(self::NUMBER_PARAM)
            ->__toString();

        $pageNameLink = Str::of(PagingParser::$param)
            ->append('[')
            ->append(self::NUMBER_PARAM)
            ->append(']')
            ->__toString();

        $paginator = $builder->simplePaginate($this->getResultSize(), ['*'], $pageNameQuery);

        if ($paginator instanceof AbstractPaginator) {
            $paginator = $paginator->setPageName($pageNameLink);

            $paginator->appends(Request::except($pageNameQuery));
        }

        return $paginator;
    }

    /**
     * Paginate the search query.
     *
     * @param SearchRequestBuilder $builder
     * @return Collection|ElasticsearchPaginator
     */
    public function applyElasticsearch(SearchRequestBuilder $builder): Collection | ElasticsearchPaginator
    {
        $pageNameQuery = Str::of(PagingParser::$param)
            ->append('.')
            ->append(self::NUMBER_PARAM)
            ->__toString();

        $pageNameLink = Str::of(PagingParser::$param)
            ->append('[')
            ->append(self::NUMBER_PARAM)
            ->append(']')
            ->__toString();

        $paginator = $builder->paginate($this->getResultSize(), $pageNameQuery)
            ->setPageName($pageNameLink)
            ->appends(Request::except($pageNameQuery));

        $paginator->setCollection($paginator->models());

        return $paginator;
    }
}
