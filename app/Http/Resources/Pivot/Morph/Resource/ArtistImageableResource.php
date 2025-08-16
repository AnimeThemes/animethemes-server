<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Morph\Resource;

use App\Http\Api\Schema\Pivot\Morph\ImageableSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Resources\BaseResource;

class ArtistImageableResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistimage';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new ImageableSchema(new ArtistSchema(), 'artistimage');
    }
}
