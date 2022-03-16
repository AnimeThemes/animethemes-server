<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Song;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\SongQuery;
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
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new SongQuery($this->validated());
    }
}
