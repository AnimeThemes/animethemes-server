<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Resources\BaseResource;

/**
 * Class SeriesResource.
 */
class SeriesResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'series';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new SeriesSchema();
    }
}
