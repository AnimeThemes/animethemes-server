<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Resource;

use App\Http\Api\Schema\Admin\FeaturedThemeSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseJsonResource;

class FeaturedThemeJsonResource extends BaseJsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'featuredtheme';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new FeaturedThemeSchema();
    }
}
