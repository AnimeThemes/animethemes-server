<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Studio;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\Studio\StudioReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

/**
 * Class StudioShowRequest.
 */
class StudioShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new StudioSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new StudioReadQuery($this->validated());
    }
}
