<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Resource;

use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

/**
 * Class PlaylistResource.
 */
class PlaylistResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'playlist';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new PlaylistSchema();
    }
}
