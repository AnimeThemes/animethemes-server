<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Studio;

use App\Http\Api\Query;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class StudioIndexRequest.
 */
class StudioIndexRequest extends IndexRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    protected function getCollection(): BaseCollection
    {
        return StudioCollection::make(new MissingValue(), Query::make());
    }
}
