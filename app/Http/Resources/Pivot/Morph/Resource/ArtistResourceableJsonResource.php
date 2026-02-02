<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Morph\Resource;

use App\Http\Api\Schema\Pivot\Morph\ResourceableSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Resources\BaseJsonResource;

class ArtistResourceableJsonResource extends BaseJsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistresource';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new ResourceableSchema(new ArtistSchema(), 'artistresource');
    }
}
