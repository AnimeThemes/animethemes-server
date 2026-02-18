<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\AnimeSynonymSchema;
use App\Http\Resources\BaseJsonResource;

class AnimeSynonymJsonResource extends BaseJsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animesynonym';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new AnimeSynonymSchema();
    }
}
