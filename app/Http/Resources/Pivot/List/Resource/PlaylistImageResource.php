<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\List\Resource;

use App\Http\Api\Schema\Pivot\List\PlaylistImageSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

class PlaylistImageResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'playlistimage';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new PlaylistImageSchema();
    }
}
