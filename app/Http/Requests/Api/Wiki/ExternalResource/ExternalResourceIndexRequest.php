<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\ExternalResource;

use App\Http\Api\Query;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ExternalResourceIndexRequest.
 */
class ExternalResourceIndexRequest extends IndexRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    protected function getCollection(): BaseCollection
    {
        return ExternalResourceCollection::make(new MissingValue(), Query::make());
    }
}
