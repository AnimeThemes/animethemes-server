<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Query;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class YearIndexRequest.
 */
class YearIndexRequest extends IndexRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    protected function getCollection(): BaseCollection
    {
        return AnimeCollection::make(new MissingValue(), Query::make());
    }

    /**
     * Get the include validation rules.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getSearchRules(): array
    {
        return [
            SearchParser::$param => [
                'prohibited',
            ],
        ];
    }
}
