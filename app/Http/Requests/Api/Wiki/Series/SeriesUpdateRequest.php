<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Series;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Series\SeriesWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Requests\Api\Base\EloquentUpdateRequest;

/**
 * Class SeriesUpdateRequest.
 */
class SeriesUpdateRequest extends EloquentUpdateRequest
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
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new SeriesWriteQuery($this->validated());
    }

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'series:update';
    }
}
