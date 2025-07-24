<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\BaseResource;
use App\Models\Wiki\Anime;

/**
 * Class AnimeResource.
 *
 * @mixin Anime
 */
class AnimeResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'anime';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new AnimeSchema();
    }
}
