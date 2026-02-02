<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Morph\Resource;

use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Pivot\Morph\ImageableSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseJsonResource;

class PlaylistImageableJsonResource extends BaseJsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'playlistimage';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new ImageableSchema(new PlaylistSchema(), 'playlistimage');
    }
}
