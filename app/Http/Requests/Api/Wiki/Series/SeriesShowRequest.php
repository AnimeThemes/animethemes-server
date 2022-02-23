<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Series;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\SeriesQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Requests\Api\EloquentShowRequest;

/**
 * Class SeriesShowRequest.
 */
class SeriesShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function getSchema(): EloquentSchema
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
        return new SeriesQuery($this->validated());
    }
}
