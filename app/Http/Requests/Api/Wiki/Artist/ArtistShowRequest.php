<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Artist;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\ArtistQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Requests\Api\EloquentShowRequest;

/**
 * Class ArtistShowRequest.
 */
class ArtistShowRequest extends EloquentShowRequest
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
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new ArtistQuery($this->validated());
    }
}
