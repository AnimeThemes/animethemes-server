<?php

declare(strict_types=1);

namespace App\Http\Resources\Document\Resource;

use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

class PageResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'page';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new PageSchema();
    }
}
