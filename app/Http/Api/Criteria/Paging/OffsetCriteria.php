<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Paging;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Parser\PagingParser;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
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
     * @param  Builder  $builder
     * @return Collection|Paginator
     */
    public function paginate(Builder $builder): Collection|Paginator
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
}
