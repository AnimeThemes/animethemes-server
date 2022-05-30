<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Series;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\Series\SeriesReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

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
    protected function schema(): EloquentSchema
    {
        return new SeriesSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new SeriesReadQuery($this->validated());
    }
}
