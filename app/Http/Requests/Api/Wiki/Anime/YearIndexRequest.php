<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Query\Wiki\AnimeQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Requests\Api\BaseRequest;

/**
 * Class YearIndexRequest.
 */
class YearIndexRequest extends BaseRequest
{
    /**
     * Get include validation rules.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getIncludeRules(): array
    {
        return [
            IncludeParser::param() => [
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

    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new AnimeSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return new AnimeQuery($this->validated());
    }
}
