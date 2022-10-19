<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\List\Playlist;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\List\Playlist\PlaylistReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;

/**
 * Class PlaylistIndexRequest.
 */
class PlaylistIndexRequest extends EloquentIndexRequest implements SearchableRequest
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
