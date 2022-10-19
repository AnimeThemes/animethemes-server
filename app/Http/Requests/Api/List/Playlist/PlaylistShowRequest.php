<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\List\Playlist;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\List\Playlist\PlaylistReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

/**
 * Class PlaylistShowRequest.
 */
class PlaylistShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new PlaylistSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new PlaylistReadQuery($this->validated());
    }
}
