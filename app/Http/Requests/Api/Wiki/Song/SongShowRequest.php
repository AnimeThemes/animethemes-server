<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Song;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\SongQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Requests\Api\EloquentShowRequest;

/**
 * Class SongShowRequest.
 */
class SongShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
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
        return SongQuery::make($this->validated());
    }
}
