<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\BaseResource;

class ThemeResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animetheme';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new ThemeSchema();
    }
}
