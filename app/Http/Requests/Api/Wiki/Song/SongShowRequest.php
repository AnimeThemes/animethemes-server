<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Song;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\SongResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SongShowRequest.
 */
class SongShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return SongResource::make(new MissingValue(), Query::make());
    }
}
