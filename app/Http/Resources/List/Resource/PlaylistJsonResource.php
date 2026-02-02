<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Resource;

use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseJsonResource;

class PlaylistJsonResource extends BaseJsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'playlist';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new PlaylistSchema();
    }
}
