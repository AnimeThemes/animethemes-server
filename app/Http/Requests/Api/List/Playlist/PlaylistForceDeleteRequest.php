<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\List\Playlist;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\List\Playlist\PlaylistWriteQuery;
use App\Http\Requests\Api\Base\EloquentForceDeleteRequest;

/**
 * Class PlaylistForceDeleteRequest.
 */
class PlaylistForceDeleteRequest extends EloquentForceDeleteRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new PlaylistWriteQuery($this->validated());
    }
}
