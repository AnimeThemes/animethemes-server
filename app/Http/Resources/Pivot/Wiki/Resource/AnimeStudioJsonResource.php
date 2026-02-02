<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\AnimeStudioSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseJsonResource;

class AnimeStudioJsonResource extends BaseJsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animestudio';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new AnimeStudioSchema();
    }
}
