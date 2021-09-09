<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\ExternalResource;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ExternalResourceShowRequest.
 */
class ExternalResourceShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return ExternalResourceResource::make(new MissingValue(), Query::make());
    }
}
