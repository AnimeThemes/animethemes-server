<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Studio;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\StudioQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Requests\Api\EloquentIndexRequest;

/**
 * Class StudioIndexRequest.
 */
class StudioIndexRequest extends EloquentIndexRequest implements SearchableRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new StudioSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return StudioQuery::make($this->validated());
    }
}
