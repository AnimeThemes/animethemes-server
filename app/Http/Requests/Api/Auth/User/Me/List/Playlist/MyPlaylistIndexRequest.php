<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth\User\Me\List\Playlist;

use App\Http\Api\Query\Auth\User\Me\List\Playlist\MyPlaylistReadQuery;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class MyPlaylistIndexRequest.
 */
class MyPlaylistIndexRequest extends EloquentIndexRequest
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
        /** @var User $user */
        $user = Auth::user();

        return new MyPlaylistReadQuery($user);
    }
}
