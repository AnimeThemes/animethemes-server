<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\ArtistMemberSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

/**
 * Class ArtistMemberResource.
 */
class ArtistMemberResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistmember';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ArtistMemberSchema();
    }
}
