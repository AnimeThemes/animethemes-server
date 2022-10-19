<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\List\Playlist\Track;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\List\Playlist\Track\TrackWriteQuery;
use App\Http\Requests\Api\Base\EloquentRestoreRequest;
use App\Models\List\Playlist;

/**
 * Class TrackRestoreRequest.
 */
class TrackRestoreRequest extends EloquentRestoreRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        /** @var Playlist $playlist */
        $playlist = $this->route('playlist');

        return new TrackWriteQuery($playlist, $this->validated());
    }
}
