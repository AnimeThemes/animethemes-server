<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Playlist\Resource;

use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

class TrackResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'track';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new TrackSchema();
    }
}
