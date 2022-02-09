<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;

/**
 * Class ShowRequest.
 */
abstract class ShowRequest extends BaseRequest
{
    /**
     * Get the paging validation rules.
     *
     * @return array
     */
    protected function getPagingRules(): array
    {
        return [
            PagingParser::param() => [
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
            SearchParser::param() => [
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
            SortParser::param() => [
                'prohibited',
            ],
        ];
    }
}
