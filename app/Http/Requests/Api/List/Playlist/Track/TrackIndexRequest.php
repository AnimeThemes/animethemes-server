<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\List\Playlist\Track;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\List\Playlist\Track\TrackReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;
use App\Models\List\Playlist;

/**
 * Class TrackIndexRequest.
 */
class TrackIndexRequest extends EloquentIndexRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new TrackSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        /** @var Playlist $playlist */
        $playlist = $this->route('playlist');

        return new TrackReadQuery($playlist, $this->validated());
    }
}
