<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Image;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ImageResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ImageShowRequest.
 */
class ImageShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return ImageResource::make(new MissingValue(), Query::make());
    }
}
