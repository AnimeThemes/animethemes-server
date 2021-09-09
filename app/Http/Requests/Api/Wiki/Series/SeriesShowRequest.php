<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Series;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SeriesShowRequest.
 */
class SeriesShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return SeriesResource::make(new MissingValue(), Query::make());
    }
}
