<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Morph\Resource;

use App\Http\Api\Schema\Pivot\Morph\ImageableSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\BaseResource;

class StudioImageableResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'studioimage';

    /**
     * Get the resource schema.
     */
    protected function schema(): Schema
    {
        return new ImageableSchema(new StudioSchema(), 'studioimage');
    }
}
