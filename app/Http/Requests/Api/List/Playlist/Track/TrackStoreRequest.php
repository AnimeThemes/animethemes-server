<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\List\Playlist\Track;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\List\Playlist\Track\TrackWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class TrackStoreRequest.
 */
class TrackStoreRequest extends EloquentStoreRequest
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
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        /** @var Playlist|null $playlist */
        $playlist = $this->route('playlist');

        $data = array_merge(
            $this->validated(),
            [PlaylistTrack::ATTRIBUTE_PLAYLIST => $playlist?->getKey()]
        );

        return new TrackWriteQuery($playlist, $data);
    }
}
