<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Video;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\VideoResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class VideoShowRequest.
 */
class VideoShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return VideoResource::make(new MissingValue(), Query::make());
    }
}
