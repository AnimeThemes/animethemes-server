<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Requests\Api\BaseRequest;

/**
 * Class YearIndexRequest.
 */
class YearIndexRequest extends BaseRequest
{
    /**
     * Get the include validation rules.
     *
     * @return array
     */
    protected function getIncludeRules(): array
    {
        return [
            IncludeParser::$param => [
                'prohibited',
            ],
        ];
    }

    /**
     * Get the paging validation rules.
     *
     * @return array
     */
    protected function getPagingRules(): array
    {
        return [
            PagingParser::$param => [
                'prohibited',
            ],
        ];
    }

    /**
     * Get the search validation rules.
     *
     * @return array
     */
    protected function getSearchRules(): array
    {
        return [
            SearchParser::$param => [
                'prohibited',
            ],
        ];
    }

    /**
     * Get the sort validation rules.
     *
     * @return array
     */
    protected function getSortRules(): array
    {
        return [
            SortParser::$param => [
                'prohibited',
            ],
        ];
    }
}
