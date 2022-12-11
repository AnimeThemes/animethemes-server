<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\List\Playlist\Forward;

use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\List\Playlist\Forward\ForwardReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\Playlist\ForwardSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;
use App\Models\List\Playlist;

/**
 * Class ForwardIndexRequest.
 */
class ForwardIndexRequest extends EloquentIndexRequest
{
    /**
     * Get the filter validation rules.
     *
     * @return array
     */
    protected function getFilterRules(): array
    {
        return $this->prohibit(FilterParser::param());
    }

    /**
     * Get the sort validation rules.
     *
     * @return array
     */
    protected function getSortRules(): array
    {
        return $this->prohibit(SortParser::param());
    }

    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new ForwardSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        /** @var Playlist $playlist */
        $playlist = $this->route('playlist');

        return new ForwardReadQuery($playlist, $this->validated());
    }
}
