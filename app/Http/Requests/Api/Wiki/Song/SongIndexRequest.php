<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Song;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\Song\SongReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;

/**
 * Class SongIndexRequest.
 */
class SongIndexRequest extends EloquentIndexRequest implements SearchableRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new SongSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new SongReadQuery($this->validated());
    }
}
