<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Artist;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ArtistShowRequest.
 */
class ArtistShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return ArtistResource::make(new MissingValue(), Query::make());
    }
}
