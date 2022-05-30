<?php

declare(strict_types=1);

namespace App\Http\Api\Parser;

use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use Illuminate\Support\Arr;

/**
 * Class PagingParser.
 */
class PagingParser extends Parser
{
    /**
     * The parameter to parse.
     *
     * @return string
     */
    public static function param(): string
    {
        return 'page';
    }

    /**
     * Parse paging from parameters.
     *
     * @param  array  $parameters
     * @return Criteria[]
     */
    public static function parse(array $parameters): array
    {
        $criteria = [];

        $pagingParam = Arr::get($parameters, static::param(), []);
        $criteria[] = static::parseLimitCriteria($pagingParam);
        $criteria[] = static::parseOffsetCriteria($pagingParam);

        return $criteria;
    }

    /**
     * Parse limit criteria instance from query string.
     *
     * @param  array  $pagingParam
     * @return LimitCriteria
     */
    protected static function parseLimitCriteria(array $pagingParam): LimitCriteria
    {
        $limit = Arr::get($pagingParam, LimitCriteria::PARAM);

        $limit = filter_var($limit, FILTER_VALIDATE_INT);

        if ($limit === false) {
            $limit = Criteria::DEFAULT_SIZE;
        }

        return new LimitCriteria($limit);
    }

    /**
     * Parse offset criteria instance from query string.
     *
     * @param  array  $pagingParam
     * @return OffsetCriteria
     */
    protected static function parseOffsetCriteria(array $pagingParam): OffsetCriteria
    {
        $size = Arr::get($pagingParam, OffsetCriteria::SIZE_PARAM);

        $size = filter_var($size, FILTER_VALIDATE_INT);

        if ($size === false) {
            $size = Criteria::DEFAULT_SIZE;
        }

        return new OffsetCriteria($size);
    }
}
