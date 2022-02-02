<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Artist;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\ArtistQuery;
use App\Http\Api\Schema\Schema;
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
     * @return Schema
     */
    protected function getSchema(): Schema
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
        return ArtistQuery::make($this->validated());
    }
}
