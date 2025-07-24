<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\ArtistImageSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

class ArtistImageResource extends BaseResource
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
        return new ArtistImageSchema();
    }
}
