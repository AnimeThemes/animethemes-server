<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Artist;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\Artist\ArtistReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;

/**
 * Class ArtistIndexRequest.
 */
class ArtistIndexRequest extends EloquentIndexRequest implements SearchableRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new ArtistSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new ArtistReadQuery($this->validated());
    }
}
