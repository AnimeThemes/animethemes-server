<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\BaseResource;

/**
 * Class VideoResource.
 */
class VideoResource extends BaseResource
{
    final public const ATTRIBUTE_LINK = 'link';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'video';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new VideoSchema();
    }
}
