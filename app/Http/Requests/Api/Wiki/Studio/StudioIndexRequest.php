<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Studio;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\Studio\StudioReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;

/**
 * Class StudioIndexRequest.
 */
class StudioIndexRequest extends EloquentIndexRequest implements SearchableRequest
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
