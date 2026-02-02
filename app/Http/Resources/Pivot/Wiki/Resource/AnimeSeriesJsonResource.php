<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\AnimeSeriesSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseJsonResource;

class AnimeSeriesJsonResource extends BaseJsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animeseries';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new AnimeSeriesSchema();
    }
}
