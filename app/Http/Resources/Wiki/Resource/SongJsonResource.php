<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Resources\BaseJsonResource;

class SongJsonResource extends BaseJsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'song';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new SongSchema();
    }
}
