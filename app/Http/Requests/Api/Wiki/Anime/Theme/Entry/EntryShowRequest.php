<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Theme\Entry;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class EntryShowRequest.
 */
class EntryShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return EntryResource::make(new MissingValue(), Query::make());
    }
}
