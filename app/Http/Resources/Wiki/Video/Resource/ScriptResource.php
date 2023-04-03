<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Video\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Resources\BaseResource;

/**
 * Class ScriptResource.
 */
class ScriptResource extends BaseResource
{
    final public const ATTRIBUTE_LINK = 'link';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'videoscript';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ScriptSchema();
    }
}
