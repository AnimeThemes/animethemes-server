<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\List\Playlist;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\List\Playlist\PlaylistWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;
use App\Models\List\Playlist;
use Illuminate\Support\Facades\Auth;

/**
 * Class PlaylistStoreRequest.
 */
class PlaylistStoreRequest extends EloquentStoreRequest
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
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        $data = array_merge(
            $this->validated(),
            [Playlist::ATTRIBUTE_USER => Auth::id()]
        );

        return new PlaylistWriteQuery($data);
    }
}
