<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Series;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\SeriesQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Requests\Api\EloquentIndexRequest;

/**
 * Class SeriesIndexRequest.
 */
class SeriesIndexRequest extends EloquentIndexRequest implements SearchableRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new SeriesSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return SeriesQuery::make($this->validated());
    }
}
